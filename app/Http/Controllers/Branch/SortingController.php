<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\SortingHistory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SortingController extends Controller
{
    /**
     * Show the Receive & Sort Bulk Page
     */
    public function receiveSort()
    {
        $user = auth()->user();
        $branch = $user->branch;

        //  Safeguard: Ensure the user is assigned to a branch
        if (!$branch) {
            return redirect()->route('branch.dashboard')
                ->with('error', 'You are not assigned to any branch. Please contact your Company Admin.');
        }

        // ১. বাল্ক প্রোডাক্টগুলো আনা (যেগুলোর স্টক > 0 এবং এই ব্রাঞ্চ বা কোম্পানির অধীনে)
        $bulkProducts = Product::where('is_bulk', true)
            ->where('stock_quantity', '>', 0)
            ->where(function ($query) use ($branch) {
                $query->where('branch_id', $branch->id)
                      ->orWhere('company_id', $branch->company_id);
            })
            ->get(['id', 'name', 'stock_quantity', 'unit']);

        // ২. রিটেইল প্রোডাক্টগুলো আনা (যেগুলো বাল্ক নয়, ড্রপডাউনে দেখানোর জন্য)
        $retailProducts = Product::where('is_bulk', false)
            ->where(function ($query) use ($branch) {
                $query->where('branch_id', $branch->id)
                      ->orWhere('company_id', $branch->company_id);
            })
            ->get(['id', 'name', 'category_id']);

        return view('branch.inventory.receive-sort', compact('bulkProducts', 'retailProducts', 'branch'));
    }

    /**
     * Store Sorted Items and Update Stock
     */
    public function storeSortedItems(Request $request)
    {
        // Validation
        $request->validate([
            'bulk_product_id' => 'required|exists:products,id',
            'bulk_quantity_received' => 'required|integer|min:1',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'sorted_items' => 'required|array|min:1',
            'sorted_items.*.product_id' => 'required|exists:products,id',
            'sorted_items.*.quantity' => 'required|integer|min:1',
            'sorted_items.*.product_type' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $branch = $user->branch;

        // 🛡️ Safeguard: Ensure the user is assigned to a branch
        if (!$branch) {
            return back()->with('error', 'You are not assigned to any branch. Please contact your Company Admin.')->withInput();
        }

        $bulkProduct = Product::findOrFail($request->bulk_product_id);

        // Backend Check: Ensure requested sort quantity does not exceed available bulk stock
        if ($request->bulk_quantity_received > $bulkProduct->stock_quantity) {
            return back()->with('error', "Insufficient bulk stock. Available: {$bulkProduct->stock_quantity}, Requested: {$request->bulk_quantity_received}")
                         ->withInput();
        }

        // Backend Check: Verify total sorted quantity matches bulk quantity exactly
        $totalSorted = collect($request->sorted_items)->sum('quantity');
        if ($totalSorted != $request->bulk_quantity_received) {
            return back()->with('error', "Sorted quantity ({$totalSorted}) must exactly match bulk quantity ({$request->bulk_quantity_received})")
                         ->withInput();
        }

        DB::beginTransaction();
        try {
            // ১. বাল্ক প্রোডাক্টের স্টক কমানো
            $bulkProduct->decrement('stock_quantity', $request->bulk_quantity_received);

            // ২. সর্ট করা রিটেইল প্রোডাক্টগুলোর স্টক বাড়ানো
            foreach ($request->sorted_items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // বাল্ক প্রোডাক্টের গড় কস্ট প্রাইস রিটেইল প্রোডাক্টে সেট করা (যদি আগে থেকে না থাকে)
                $averageCost = $bulkProduct->cost_price ?? 0;
                
                $product->increment('stock_quantity', $item['quantity']);
                
                if (!$product->cost_price || $product->cost_price == 0) {
                    $product->update(['cost_price' => $averageCost]);
                }
            }

            // ৩. সর্টিং হিস্টোরি রেকর্ড সেভ করা
            SortingHistory::create([
                'branch_id' => $branch->id,
                'bulk_product_id' => $bulkProduct->id,
                'bulk_product_name' => $bulkProduct->name,
                'bulk_quantity_received' => $request->bulk_quantity_received,
                'sorted_items' => $request->sorted_items, // JSON হিসেবে সেভ হবে (Model-এ cast করা আছে)
                'user_id' => auth()->id(),
                'sorted_at' => Carbon::now(),
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
            ]);

            DB::commit();
            
            return redirect()->route('branch.inventory.receive-sort')
                ->with('success', 'Bulk items sorted and stock updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            // লগিংয়ের জন্য এটি রাখা ভালো: Log::error('Sorting Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while sorting: ' . $e->getMessage())
                         ->withInput();
        }
    }

    /**
     * Show Sorting History Page
     */
    public function history()
    {
        $user = auth()->user();
        $branch = $user->branch;

        //  Safeguard: Ensure the user is assigned to a branch
        if (!$branch) {
            return redirect()->route('branch.dashboard')
                ->with('error', 'You are not assigned to any branch. Please contact your Company Admin.');
        }
        
        $histories = SortingHistory::where('branch_id', $branch->id)
            ->with(['bulkProduct', 'user'])
            ->orderBy('sorted_at', 'desc')
            ->paginate(20);

        return view('branch.inventory.sorting-history', compact('histories'));
    }

    /**
     * View Single Sorting History Details
     */
    public function showHistory($id)
    {
        $user = auth()->user();
        $branch = $user->branch;

        //  Safeguard: Ensure the user is assigned to a branch
        if (!$branch) {
            return redirect()->route('branch.dashboard')
                ->with('error', 'You are not assigned to any branch. Please contact your Company Admin.');
        }
        
        $history = SortingHistory::where('branch_id', $branch->id)
            ->with(['bulkProduct', 'user'])
            ->findOrFail($id);

        return view('branch.inventory.sorting-history-detail', compact('history'));
    }
}