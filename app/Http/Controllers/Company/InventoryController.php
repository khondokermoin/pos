<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display low stock alerts for this company.
     */
    public function lowStock()
    {
        $companyId = Auth::user()->company_id;

        $lowStockItems = Stock::with(['variant.product.category', 'variant.unit', 'branch'])
            ->whereHas('variant.product', fn($q) => $q->where('company_id', $companyId))
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->orderBy('quantity', 'asc')
            ->paginate(20);

        $outOfStockCount = Stock::whereHas('variant.product', fn($q) => $q->where('company_id', $companyId))
            ->where('quantity', 0)
            ->count();

        $criticalCount = Stock::whereHas('variant.product', fn($q) => $q->where('company_id', $companyId))
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->where('quantity', '>', 0)
            ->count();

        return view('company.inventory.low-stock', compact('lowStockItems', 'outOfStockCount', 'criticalCount'));
    }

    /**
     * Show the stock adjustment form and recent adjustments.
     */
    public function stockAdjust()
    {
        $companyId = Auth::user()->company_id;

        $branches = Branch::where('company_id', $companyId)->where('status', 'active')->get();

        $variants = ProductVariant::whereHas('product', function ($q) use ($companyId) {
            $q->where('company_id', $companyId)->where('is_active', true);
        })->where('is_active', true)->with('product')->get();

        $recentAdjustments = StockMovement::with(['variant.product', 'branch', 'user'])
            ->where('company_id', $companyId)
            ->where('type', 'adjustment')
            ->latest()
            ->take(10)
            ->get();

        return view('company.inventory.stock_adjust', compact('branches', 'variants', 'recentAdjustments'));
    }

    /**
     * Apply a stock adjustment.
     */
    public function storeAdjustment(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'branch_id'  => 'nullable|exists:branches,id',
            'type'       => 'required|in:add,subtract,set',
            'quantity'   => 'required|integer|min:1',
            'reason'     => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $stock = Stock::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'variant_id' => $request->variant_id,
                    'branch_id'  => $request->branch_id,
                ],
                ['quantity' => 0, 'reorder_level' => 5]
            );

            $before = $stock->quantity;

            match ($request->type) {
                'add'      => $stock->increment('quantity', $request->quantity),
                'subtract' => $stock->decrement('quantity', $request->quantity),
                'set'      => $stock->update(['quantity' => $request->quantity]),
            };

            $stock->refresh();

            StockMovement::create([
                'company_id' => $companyId,
                'variant_id' => $request->variant_id,
                'branch_id'  => $request->branch_id,
                'type'       => 'adjustment',
                'quantity'   => $request->quantity,
                'reference'  => 'Manual Adjustment: ' . ($request->reason ?? 'No reason given') .
                    " (Before: {$before}, After: {$stock->quantity})",
                'user_id'    => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('company.inventory.stock-adjust')
                ->with('success', "Stock adjusted successfully. New quantity: {$stock->quantity}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Adjustment failed: ' . $e->getMessage())->withInput();
        }
    }
}
