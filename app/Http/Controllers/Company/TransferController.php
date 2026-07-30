<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    /**
     * Display a listing of stock transfers.
     */
    public function index()
    {
        $companyId = Auth::user()->company_id;

        $transfers = StockTransfer::with(['fromBranch', 'toBranch', 'user'])
            ->where('company_id', $companyId)
            ->latest()
            ->paginate(15);

        $totalTransfers = StockTransfer::where('company_id', $companyId)->count();
        $completedCount = StockTransfer::where('company_id', $companyId)->where('status', 'completed')->count();
        $pendingCount   = StockTransfer::where('company_id', $companyId)->where('status', 'pending')->count();

        return view('company.transfers.index', compact(
            'transfers',
            'totalTransfers',
            'completedCount',
            'pendingCount'
        ));
    }

    /**
     * Show the form for creating a new stock transfer.
     */
    public function create()
    {
        $companyId = Auth::user()->company_id;

        $branches = Branch::where('company_id', $companyId)->where('status', 'active')->get();

        $variants = ProductVariant::whereHas('product', function ($q) use ($companyId) {
            $q->where('company_id', $companyId)->where('is_active', true);
        })->where('is_active', true)
            ->with(['product', 'stock'])
            ->get();

        return view('company.transfers.create', compact('branches', 'variants'));
    }

    /**
     * Execute the stock transfer — deduct from source, add to destination.
     */
    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $request->validate([
            'from_branch_id'     => 'nullable|exists:branches,id',
            'to_branch_id'       => 'required|exists:branches,id',
            'transfer_date'      => 'required|date',
            'note'               => 'nullable|string|max:500',
            'items'              => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        // Ensure source and destination are different
        if ($request->from_branch_id && $request->from_branch_id == $request->to_branch_id) {
            return back()->withInput()->with('error', 'Source and destination branch cannot be the same.');
        }

        DB::beginTransaction();
        try {
            // 1. Create the transfer record
            $transfer = StockTransfer::create([
                'company_id'     => $companyId,
                'from_branch_id' => $request->from_branch_id,
                'to_branch_id'   => $request->to_branch_id,
                'user_id'        => Auth::id(),
                'transfer_date'  => $request->transfer_date,
                'status'         => 'completed',
                'note'           => $request->note,
                'reference_no'   => 'TRF-' . strtoupper(substr(uniqid(), -6)),
            ]);

            $itemCount = 0;

            foreach ($request->items as $item) {
                $variantId = $item['variant_id'];
                $qty       = (int) $item['quantity'];

                // 2. Check source stock
                $sourceStock = Stock::where('company_id', $companyId)
                    ->where('variant_id', $variantId)
                    ->where('branch_id', $request->from_branch_id)
                    ->first();

                if (!$sourceStock || $sourceStock->quantity < $qty) {
                    $variantSku = optional(ProductVariant::find($variantId))->sku ?? "Variant #{$variantId}";
                    throw new \Exception(
                        "Insufficient stock for SKU: {$variantSku}. " .
                            "Available: " . ($sourceStock?->quantity ?? 0) . ", Requested: {$qty}"
                    );
                }

                // 3. Deduct from source
                $sourceStock->decrement('quantity', $qty);

                // 4. Add to destination
                Stock::updateOrCreate(
                    [
                        'company_id' => $companyId,
                        'variant_id' => $variantId,
                        'branch_id'  => $request->to_branch_id,
                    ],
                    ['quantity' => 0, 'reorder_level' => $sourceStock->reorder_level ?? 5]
                );
                Stock::where('company_id', $companyId)
                    ->where('variant_id', $variantId)
                    ->where('branch_id', $request->to_branch_id)
                    ->increment('quantity', $qty);

                $toBranchName   = Branch::find($request->to_branch_id)?->name ?? 'Unknown';
                $fromBranchName = $request->from_branch_id
                    ? (Branch::find($request->from_branch_id)?->name ?? 'Unknown')
                    : 'Central Warehouse';

                // 5. Log stock movements
                StockMovement::create([
                    'company_id'     => $companyId,
                    'variant_id'     => $variantId,
                    'branch_id'      => $request->from_branch_id,
                    'type'           => 'transfer_out',
                    'quantity'       => $qty,
                    'reference'      => "Transfer #{$transfer->reference_no} → {$toBranchName}",
                    'reference_type' => StockTransfer::class,
                    'reference_id'   => $transfer->id,
                    'user_id'        => Auth::id(),
                ]);

                StockMovement::create([
                    'company_id'     => $companyId,
                    'variant_id'     => $variantId,
                    'branch_id'      => $request->to_branch_id,
                    'type'           => 'transfer_in',
                    'quantity'       => $qty,
                    'reference'      => "Transfer #{$transfer->reference_no} ← {$fromBranchName}",
                    'reference_type' => StockTransfer::class,
                    'reference_id'   => $transfer->id,
                    'user_id'        => Auth::id(),
                ]);

                $itemCount++;
            }

            DB::commit();
            return redirect()->route('company.transfers.index')
                ->with('success', "Transfer {$transfer->reference_no} completed. {$itemCount} item(s) moved from {$fromBranchName} to {$toBranchName}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }
}
