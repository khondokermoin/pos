<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    private function companyId(): int
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId();

        $query = Quotation::with('customer')
            ->where('company_id', $companyId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('quotation_no', 'like', '%' . $request->search . '%')
                    ->orWhereHas('customer', fn($c) => $c->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $quotations = $query->latest()->paginate(20);

        return view('company.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $companyId = $this->companyId();

        $customers = Customer::where('company_id', $companyId)->orderBy('name')->get();
        $products  = ProductVariant::with('product')
            ->whereHas('product', fn($q) => $q->where('company_id', $companyId))
            ->get();

        return view('company.quotations.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'        => 'nullable|exists:customers,id',
            'valid_until'        => 'nullable|date|after_or_equal:today',
            'notes'              => 'nullable|string|max:1000',
            'items'              => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.qty'        => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
        ]);

        $companyId = $this->companyId();

        DB::transaction(function () use ($data, $companyId) {
            // lockForUpdate serializes concurrent requests for this company so two
            // simultaneous quotations can't compute the same next number.
            $lastNo = Quotation::where('company_id', $companyId)
                ->lockForUpdate()
                ->count() + 1;
            $quotationNo = 'QT-' . date('Ymd') . '-' . str_pad($lastNo, 4, '0', STR_PAD_LEFT);

            $subtotal = collect($data['items'])->sum(fn($i) => $i['qty'] * $i['price']);

            $quotation = Quotation::create([
                'company_id'   => $companyId,
                'customer_id'  => $data['customer_id'] ?? null,
                'quotation_no' => $quotationNo,
                'subtotal'     => $subtotal,
                'discount'     => 0,
                'total_amount' => $subtotal,
                'valid_until'  => $data['valid_until'] ?? null,
                'status'       => 'draft',
                'notes'        => $data['notes'] ?? null,
                'created_by'   => Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'variant_id'   => $item['variant_id'],
                    'qty'          => $item['qty'],
                    'price'        => $item['price'],
                    'subtotal'     => $item['qty'] * $item['price'],
                ]);
            }
        });

        return redirect()->route('company.quotations.index')
            ->with('success', 'Quotation created successfully.');
    }

    public function show(string $id)
    {
        $companyId = $this->companyId();

        $quotation = Quotation::with(['customer', 'items.variant.product', 'createdBy', 'convertedToSale'])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        $branches = Branch::where('company_id', $companyId)->get();

        return view('company.quotations.show', compact('quotation', 'branches'));
    }

    public function updateStatus(Request $request, string $id)
    {
        $quotation = Quotation::where('company_id', $this->companyId())->findOrFail($id);

        $request->validate([
            'status' => 'required|in:draft,sent,accepted,rejected,expired',
        ]);

        $quotation->update(['status' => $request->status]);

        return redirect()->route('company.quotations.show', $id)
            ->with('success', 'Quotation status updated.');
    }

    public function convertToSale(Request $request, string $id)
    {
        $companyId = $this->companyId();

        $quotation = Quotation::with('items')
            ->where('company_id', $companyId)
            ->findOrFail($id);

        if ($quotation->status !== 'accepted') {
            return back()->with('error', 'Only accepted quotations can be converted to a sale.');
        }

        if ($quotation->converted_to_sale_id) {
            return back()->with('error', 'This quotation has already been converted to a sale.');
        }

        $data = $request->validate([
            'branch_id'      => 'required|exists:branches,id,company_id,' . $companyId,
            'payment_method' => 'required|in:cash,card,mobile_banking',
        ]);

        try {
            $sale = DB::transaction(function () use ($quotation, $data, $companyId) {
                $branchId = $data['branch_id'];

                foreach ($quotation->items as $item) {
                    $stock = Stock::where('variant_id', $item->variant_id)
                        ->where('branch_id', $branchId)
                        ->lockForUpdate()
                        ->first();

                    if (! $stock || $stock->quantity < $item->qty) {
                        $variantName = optional(optional($item->variant)->product)->name ?? 'item';
                        throw new \RuntimeException(
                            "Insufficient stock for \"{$variantName}\" at the selected branch. Available: " . ($stock->quantity ?? 0) . ", required: {$item->qty}."
                        );
                    }
                }

                $invoiceNo = 'INV-' . strtoupper(base_convert(time(), 10, 36)) . '-' . strtoupper(substr(uniqid(), -4));

                $sale = Sale::create([
                    'company_id'      => $companyId,
                    'branch_id'       => $branchId,
                    'customer_id'     => $quotation->customer_id,
                    'user_id'         => Auth::id(),
                    'invoice_no'      => $invoiceNo,
                    'subtotal'        => $quotation->subtotal,
                    'discount'        => $quotation->discount,
                    'total_amount'    => $quotation->total_amount,
                    'received_amount' => $quotation->total_amount,
                    'payment_method'  => $data['payment_method'],
                    'status'          => 'completed',
                ]);

                foreach ($quotation->items as $item) {
                    $sale->items()->create([
                        'variant_id'   => $item->variant_id,
                        'product_name' => optional(optional($item->variant)->product)->name ?? 'Item',
                        'quantity'     => $item->qty,
                        'unit_price'   => $item->price,
                        'subtotal'     => $item->subtotal,
                    ]);

                    Stock::where('variant_id', $item->variant_id)
                        ->where('branch_id', $branchId)
                        ->decrement('quantity', $item->qty);

                    StockMovement::create([
                        'company_id'     => $companyId,
                        'branch_id'      => $branchId,
                        'variant_id'     => $item->variant_id,
                        'type'           => 'sale_out',
                        'quantity'       => -$item->qty,
                        'reference_type' => Sale::class,
                        'reference_id'   => $sale->id,
                        'user_id'        => Auth::id(),
                    ]);
                }

                $quotation->update(['converted_to_sale_id' => $sale->id]);

                return $sale;
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('company.quotations.show', $quotation->id)
            ->with('success', 'Quotation converted to Sale #' . $sale->invoice_no . ' successfully.');
    }

    public function destroy(string $id)
    {
        Quotation::where('company_id', $this->companyId())->findOrFail($id)->delete();

        return redirect()->route('company.quotations.index')
            ->with('success', 'Quotation deleted successfully.');
    }
}
