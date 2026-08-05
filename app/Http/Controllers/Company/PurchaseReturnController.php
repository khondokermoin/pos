<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    private function companyId(): int
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId();

        $query = PurchaseReturn::with(['purchase.supplier'])
            ->where('company_id', $companyId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $returns = $query->latest()->paginate(20);

        return view('company.purchase_returns.index', compact('returns'));
    }

    public function create()
    {
        $companyId = $this->companyId();

        $purchases = Purchase::where('company_id', $companyId)
            ->with('supplier')
            ->latest()
            ->limit(100)
            ->get();

        return view('company.purchase_returns.create', compact('purchases'));
    }

    public function getPurchaseItems(string $purchaseId)
    {
        $purchase = Purchase::with('items.variant.product')
            ->where('company_id', $this->companyId())
            ->findOrFail($purchaseId);

        return response()->json($purchase->items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'purchase_id'                  => 'required|exists:purchases,id',
            'reason'                       => 'required|string|max:500',
            'items'                        => 'required|array|min:1',
            'items.*.purchase_item_id'     => 'required|exists:purchase_items,id',
            'items.*.qty'                  => 'required|integer|min:1',
        ]);

        $companyId = $this->companyId();

        DB::transaction(function () use ($data, $companyId) {
            $lastNo   = PurchaseReturn::where('company_id', $companyId)->count() + 1;
            $returnNo = 'PR-' . date('Ymd') . '-' . str_pad($lastNo, 4, '0', STR_PAD_LEFT);

            $totalAmount = 0;

            $purchaseReturn = PurchaseReturn::create([
                'company_id'  => $companyId,
                'purchase_id' => $data['purchase_id'],
                'return_no'   => $returnNo,
                'total_amount' => 0,
                'reason'      => $data['reason'],
                'status'      => 'approved',
                'created_by'  => Auth::id(),
            ]);

            $purchase = Purchase::find($data['purchase_id']);

            foreach ($data['items'] as $item) {
                $purchaseItem = PurchaseItem::findOrFail($item['purchase_item_id']);
                $subtotal     = $purchaseItem->unit_price * $item['qty'];
                $totalAmount += $subtotal;

                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'purchase_item_id'   => $item['purchase_item_id'],
                    'qty'                => $item['qty'],
                    'price'              => $purchaseItem->unit_price,
                    'subtotal'           => $subtotal,
                ]);

                // Reduce stock (items returned to supplier)
                $stock = Stock::where('variant_id', $purchaseItem->variant_id)
                    ->where('branch_id', $purchase->branch_id)
                    ->first();
                if ($stock && $stock->quantity >= $item['qty']) {
                    $stock->decrement('quantity', $item['qty']);
                }
            }

            $purchaseReturn->update(['total_amount' => $totalAmount]);
        });

        return redirect()->route('company.purchase-returns.index')
            ->with('success', 'Purchase return recorded successfully.');
    }

    public function show(string $id)
    {
        $return = PurchaseReturn::with(['purchase.supplier', 'items.purchaseItem.variant.product'])
            ->where('company_id', $this->companyId())
            ->findOrFail($id);

        return view('company.purchase_returns.show', compact('return'));
    }

    public function destroy(string $id)
    {
        PurchaseReturn::where('company_id', $this->companyId())->findOrFail($id)->delete();

        return redirect()->route('company.purchase-returns.index')
            ->with('success', 'Purchase return deleted successfully.');
    }
}
