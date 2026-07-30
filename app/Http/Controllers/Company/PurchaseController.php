<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the purchases.
     */
    public function index()
    {
        $companyId = auth()->user()->company_id;
        
        $purchases = Purchase::with(['branch', 'supplier', 'user'])
            ->where('company_id', $companyId)
            ->latest()
            ->paginate(15);
            
        return view('company.purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new purchase.
     */
    public function create()
    {
        $companyId = auth()->user()->company_id;
        
        $branches = Branch::where('company_id', $companyId)->get();
        $suppliers = Supplier::where('company_id', $companyId)->get();
        
        // সব Active Product Variant আনা (Company Level)
        $variants = ProductVariant::whereHas('product', function ($q) use ($companyId) {
            $q->where('company_id', $companyId)->where('is_active', true);
        })->where('is_active', true)->with('product')->get();

        return view('company.purchases.create', compact('branches', 'suppliers', 'variants'));
    }

    /**
     * Store a newly created purchase in storage.
     * Standard Business Logic: branch_id can be NULL for Central Warehouse.
     */
    public function store(Request $request)
    {
        // ⚠️ গুরুত্বপূর্ণ আপডেট: branch_id এখন 'nullable', যাতে Head Office / Central Warehouse-এর পারচেজ এন্ট্রি দেওয়া যায়
        $request->validate([
            'branch_id' => 'nullable|exists:branches,id', 
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $companyId = auth()->user()->company_id;
        $userId = auth()->id();

        DB::beginTransaction();
        try {
            // ১. Purchase রেকর্ড তৈরি (branch_id null থাকলে তা Central Warehouse হিসেবে গণ্য হবে)
            $purchase = Purchase::create([
                'company_id' => $companyId,
                'branch_id' => $request->branch_id, 
                'supplier_id' => $request->supplier_id,
                'user_id' => $userId,
                'purchase_date' => $request->purchase_date,
                'total_amount' => $request->total_amount,
                'status' => 'completed', 
            ]);

            // ২. প্রতিটি আইটেমের জন্য Stock আপডেট এবং Movement লগ তৈরি
            foreach ($request->items as $item) {
                // Purchase Item সেভ
                $purchase->items()->create([
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);

                // Stock আপডেট (না থাকলে তৈরি করবে, থাকলে আপডেট করবে)
                Stock::updateOrCreate(
                    [
                        'company_id' => $companyId,
                        'branch_id' => $request->branch_id, // এটি null হতে পারে (Central Warehouse)
                        'variant_id' => $item['variant_id'],
                    ],
                    [
                        // (int) ব্যবহার করা হয়েছে SQL Injection বা টাইপ এরর প্রতিরোধের জন্য
                        'quantity' => DB::raw('quantity + ' . (int)$item['quantity']),
                    ]
                );

                // Stock Movement লগ তৈরি
                StockMovement::create([
                    'company_id' => $companyId,
                    'branch_id' => $request->branch_id, // এটি null হতে পারে
                    'variant_id' => $item['variant_id'],
                    'type' => 'purchase_in',
                    'quantity' => (int)$item['quantity'],
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'user_id' => $userId,
                ]);
            }

            DB::commit();
            return redirect()->route('company.purchases.index')
                ->with('success', 'Purchase successful and stock updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            // ডিবাগিংয়ের সুবিধার্থে এররটি লগে রাখা ভালো
            \Log::error('Purchase failed: ' . $e->getMessage());
            return back()->with('error', 'Purchase failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified purchase.
     */
    public function show(Purchase $purchase)
    {
        // Security Check: শুধুমাত্র নিজের কোম্পানির পারচেজ দেখতে পারবে
        if ($purchase->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $purchase->load(['items.variant.product', 'branch', 'supplier', 'user']);
        return view('company.purchases.show', compact('purchase'));
    }

    /**
     * (Optional) Remove the specified purchase from storage.
     * আপনি চাইলে এই মেথডটি রাখতে পারেন অথবা বাদ দিতে পারেন।
     */
    public function destroy(Purchase $purchase)
    {
        if ($purchase->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        DB::beginTransaction();
        try {
            // প্রয়োজনে এখানে স্টক রিভার্স (Stock Reverse) করার লজিক যোগ করতে পারেন
            $purchase->delete();
            DB::commit();
            
            return redirect()->route('company.purchases.index')
                ->with('success', 'Purchase deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete purchase: ' . $e->getMessage());
        }
    }
}