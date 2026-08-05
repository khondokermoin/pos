<?php

namespace App\Http\Controllers\Branch;

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
    private function branchId(): int
    {
        return Auth::user()->branch_id;
    }

    private function companyId(): int
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        $branchId = $this->branchId();

        $returns = SalesReturn::with(['sale.customer'])
            ->whereHas('sale', fn($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->paginate(20);

        return view('branch.sales_returns.index', compact('returns'));
    }

    public function create()
    {
        $sales = Sale::where('branch_id', $this->branchId())
            ->with('customer')
            ->latest()
            ->limit(100)
            ->get();

        return view('branch.sales_returns.create', compact('sales'));
    }

    public function getSaleItems(string $saleId)
    {
        $sale = Sale::with('items.variant.product')
            ->where('branch_id', $this->branchId())
            ->findOrFail($saleId);

        return response()->json($sale->items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sale_id'              => 'required|exists:sales,id',
            'reason'               => 'required|string|max:500',
            'items'                => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.qty'          => 'required|integer|min:1',
        ]);

        $companyId = $this->companyId();
        $branchId  = $this->branchId();

        DB::transaction(function () use ($data, $companyId, $branchId) {
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
                $saleItem = SaleItem::findOrFail($item['sale_item_id']);
                $subtotal = $saleItem->unit_price * $item['qty'];
                $totalAmount += $subtotal;

                SalesReturnItem::create([
                    'sales_return_id' => $salesReturn->id,
                    'sale_item_id'    => $item['sale_item_id'],
                    'qty'             => $item['qty'],
                    'price'           => $saleItem->unit_price,
                    'subtotal'        => $subtotal,
                ]);

                // Restore stock at this branch
                $stock = Stock::where('variant_id', $saleItem->variant_id)
                    ->where('branch_id', $branchId)
                    ->first();
                if ($stock) {
                    $stock->increment('quantity', $item['qty']);
                }
            }

            $salesReturn->update(['total_amount' => $totalAmount]);
        });

        return redirect()->route('branch.sales-returns.index')
            ->with('success', 'Sales return recorded successfully.');
    }

    public function show(string $id)
    {
        $return = SalesReturn::with(['sale.customer', 'items.saleItem.variant.product'])
            ->whereHas('sale', fn($q) => $q->where('branch_id', $this->branchId()))
            ->findOrFail($id);

        return view('branch.sales_returns.show', compact('return'));
    }
}
