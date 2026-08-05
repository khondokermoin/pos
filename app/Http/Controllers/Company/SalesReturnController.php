<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends Controller
{
    private function companyId(): int
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId();

        $query = SalesReturn::with(['sale.customer'])
            ->where('company_id', $companyId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $returns = $query->latest()->paginate(20);

        return view('company.sales_returns.index', compact('returns'));
    }

    public function create()
    {
        $companyId = $this->companyId();

        $sales = Sale::where('company_id', $companyId)
            ->with('customer')
            ->latest()
            ->limit(100)
            ->get();

        return view('company.sales_returns.create', compact('sales'));
    }

    public function getSaleItems(string $saleId)
    {
        $sale = Sale::with('items.variant.product')
            ->where('company_id', $this->companyId())
            ->findOrFail($saleId);

        return response()->json($sale->items);
    }

    public function store(Request $request)
    {
        $companyId = $this->companyId();

        $data = $request->validate([
            'sale_id'              => 'required|exists:sales,id,company_id,' . $companyId,
            'reason'               => 'required|string|max:500',
            'items'                => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.qty'          => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($data, $companyId) {
            $lastNo   = SalesReturn::where('company_id', $companyId)->count() + 1;
            $returnNo = 'SR-' . date('Ymd') . '-' . str_pad($lastNo, 4, '0', STR_PAD_LEFT);

            $totalAmount = 0;

            $salesReturn = SalesReturn::create([
                'company_id'   => $companyId,
                'sale_id'      => $data['sale_id'],
                'return_no'    => $returnNo,
                'total_amount' => 0,
                'reason'       => $data['reason'],
                'status'       => 'approved',
                'created_by'   => Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                // Security: verify the sale_item belongs to a sale owned by this company.
                $saleItem = SaleItem::whereHas(
                    'sale',
                    fn($q) => $q->where('company_id', $companyId)
                )->findOrFail($item['sale_item_id']);
                $subtotal = $saleItem->unit_price * $item['qty'];
                $totalAmount += $subtotal;

                SalesReturnItem::create([
                    'sales_return_id' => $salesReturn->id,
                    'sale_item_id'    => $item['sale_item_id'],
                    'qty'             => $item['qty'],
                    'price'           => $saleItem->unit_price,
                    'subtotal'        => $subtotal,
                ]);

                // Restore stock
                $stock = Stock::where('variant_id', $saleItem->variant_id)
                    ->where('branch_id', Sale::find($data['sale_id'])->branch_id)
                    ->first();
                if ($stock) {
                    $stock->increment('quantity', $item['qty']);
                }
            }

            $salesReturn->update(['total_amount' => $totalAmount]);
        });

        return redirect()->route('company.sales-returns.index')
            ->with('success', 'Sales return recorded successfully.');
    }

    public function show(string $id)
    {
        $return = SalesReturn::with(['sale.customer', 'items.saleItem.variant.product'])
            ->where('company_id', $this->companyId())
            ->findOrFail($id);

        return view('company.sales_returns.show', compact('return'));
    }

    public function destroy(string $id)
    {
        SalesReturn::where('company_id', $this->companyId())->findOrFail($id)->delete();

        return redirect()->route('company.sales-returns.index')
            ->with('success', 'Sales return deleted successfully.');
    }
}
