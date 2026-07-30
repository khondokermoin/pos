<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\Tax;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $companyId = Auth::user()->company_id;

        $products = Product::where('company_id', $companyId)
            ->with(['category', 'brand', 'variants.stock'])
            ->latest()
            ->paginate(15);

        return view('company.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $companyId = Auth::user()->company_id;

        $categories = Category::where('company_id', $companyId)->get(); 
        $brands     = Brand::where('company_id', $companyId)->get();
        $units      = Unit::where('company_id', $companyId)->get();
        $taxes      = Tax::where('company_id', $companyId)->get();

        return view('company.products.create', compact('categories', 'brands', 'units', 'taxes'));
    }

    /**
     * Store a newly created product and its variant(s) in storage.
     */
    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $userId    = Auth::id();

        // ১. সম্পূর্ণ ফর্ম ভ্যালিডেশন (Company scoped exists rules added for security)
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id,company_id,' . $companyId,
            'brand_id'     => 'nullable|exists:brands,id,company_id,' . $companyId,
            'description'  => 'nullable|string',
            'has_variants' => 'nullable|boolean',
            
            'variants'                 => 'required|array|min:1',
            'variants.*.sku'           => 'required|string|max:255',
            'variants.*.barcode'       => 'nullable|string|max:255',
            'variants.*.unit_id'       => 'required|exists:units,id,company_id,' . $companyId,
            'variants.*.tax_id'        => 'nullable|exists:taxes,id,company_id,' . $companyId,
            'variants.*.cost_price'    => 'required|numeric|min:0',
            'variants.*.selling_price' => 'required|numeric|min:0',
            'variants.*.stock'         => 'required|integer|min:0',
            'variants.*.reorder_level' => 'required|integer|min:0',
            'variants.*.attributes'    => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            // ক) মূল প্রোডাক্ট তৈরি
            $product = Product::create([
                'company_id'   => $companyId,
                'name'         => $validated['name'],
                'category_id'  => $validated['category_id'],
                'brand_id'     => $validated['brand_id'] ?? null,
                'description'  => $validated['description'] ?? null,
                'has_variants' => $request->boolean('has_variants'),
            ]);

            // খ) ভেরিয়েন্ট(গুলো) প্রসেস এবং তৈরি করা
            foreach ($validated['variants'] as $variantData) {
                $cleanAttributes = [];
                if (!empty($variantData['attributes']) && is_array($variantData['attributes'])) {
                    foreach ($variantData['attributes'] as $attr) {
                        if (!empty($attr['key']) && !empty($attr['value'])) {
                            $cleanAttributes[] = [
                                'key'   => trim($attr['key']),
                                'value' => trim($attr['value'])
                            ];
                        }
                    }
                }
                
                $variantData['attributes'] = !empty($cleanAttributes) ? json_encode($cleanAttributes) : null;
                $variantData['product_id'] = $product->id;

                $variant = ProductVariant::create($variantData);

                // গ) ইনিশিয়াল স্টক এবং Stock Movement এন্ট্রি
                $initialStock = (int) ($variantData['stock'] ?? 0);
                if ($initialStock > 0) {
                    Stock::updateOrCreate(
                        [
                            'company_id' => $companyId,
                            'product_id' => $product->id,
                            'variant_id' => $variant->id,
                        ],
                        [
                            'quantity'      => $initialStock,
                            'reorder_level' => $variantData['reorder_level'],
                        ]
                    );

                    StockMovement::create([
                        'company_id' => $companyId,
                        'product_id' => $product->id,
                        'variant_id' => $variant->id,
                        'type'       => 'in',
                        'quantity'   => $initialStock,
                        'reference'  => 'Initial Stock Entry',
                        'user_id'    => $userId,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('company.products.index')->with('success', 'পণ্য এবং এর ভেরিয়েন্ট সফলভাবে যোগ করা হয়েছে!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'পণ্য যোগ করতে সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $this->authorizeCompany($product);
        
        $product->load(['category', 'brand', 'variants.stock', 'variants.tax']);
        return view('company.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $this->authorizeCompany($product);
        $companyId = Auth::user()->company_id;

        // ভেরিয়েন্টের সাথে লোড করা হচ্ছে
        $product->load('variants');
        
        // JSON attributes কে আবার Array তে রূপান্তর করা হচ্ছে যাতে Blade এ old() এর মতো কাজ করে
        foreach ($product->variants as $variant) {
            if ($variant->attributes) {
                $variant->attributes = json_decode($variant->attributes, true);
            } else {
                $variant->attributes = [];
            }
        }

        $categories = Category::where('company_id', $companyId)->where('status', 'active')->get();
        $brands     = Brand::where('company_id', $companyId)->where('status', 'active')->get();
        $units      = Unit::where('company_id', $companyId)->where('status', 'active')->get();
        $taxes      = Tax::where('company_id', $companyId)->where('status', 'active')->get();

        return view('company.products.edit', compact('product', 'categories', 'brands', 'units', 'taxes'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $this->authorizeCompany($product);
        $companyId = Auth::user()->company_id;
        $userId    = Auth::id();

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id,company_id,' . $companyId,
            'brand_id'     => 'nullable|exists:brands,id,company_id,' . $companyId,
            'description'  => 'nullable|string',
            'has_variants' => 'nullable|boolean',
            
            'variants'                 => 'required|array|min:1',
            'variants.*.id'            => 'nullable|exists:product_variants,id', // Edit এর জন্য নতুন যুক্ত করা হয়েছে
            'variants.*.sku'           => 'required|string|max:255',
            'variants.*.barcode'       => 'nullable|string|max:255',
            'variants.*.unit_id'       => 'required|exists:units,id,company_id,' . $companyId,
            'variants.*.tax_id'        => 'nullable|exists:taxes,id,company_id,' . $companyId,
            'variants.*.cost_price'    => 'required|numeric|min:0',
            'variants.*.selling_price' => 'required|numeric|min:0',
            'variants.*.stock'         => 'required|integer|min:0',
            'variants.*.reorder_level' => 'required|integer|min:0',
            'variants.*.attributes'    => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            // ১. মূল প্রোডাক্ট আপডেট
            $product->update([
                'name'         => $validated['name'],
                'category_id'  => $validated['category_id'],
                'brand_id'     => $validated['brand_id'] ?? null,
                'description'  => $validated['description'] ?? null,
                'has_variants' => $request->boolean('has_variants'),
            ]);

            $processedVariantIds = [];

            // ২. ভেরিয়েন্ট আপডেট বা নতুন তৈরি
            foreach ($validated['variants'] as $variantData) {
                $cleanAttributes = [];
                if (!empty($variantData['attributes']) && is_array($variantData['attributes'])) {
                    foreach ($variantData['attributes'] as $attr) {
                        if (!empty($attr['key']) && !empty($attr['value'])) {
                            $cleanAttributes[] = [
                                'key'   => trim($attr['key']),
                                'value' => trim($attr['value'])
                            ];
                        }
                    }
                }
                
                $variantData['attributes'] = !empty($cleanAttributes) ? json_encode($cleanAttributes) : null;
                
                $variantId = $variantData['id'] ?? null;
                unset($variantData['id']); // updateOrCreate এ id আলাদাভাবে লাগে

                if ($variantId) {
                    // existing variant update
                    $variant = ProductVariant::where('product_id', $product->id)->findOrFail($variantId);
                    $variant->update($variantData);
                    $processedVariantIds[] = $variant->id;
                } else {
                    // new variant create
                    $variantData['product_id'] = $product->id;
                    $newVariant = ProductVariant::create($variantData);
                    $processedVariantIds[] = $newVariant->id;

                    // নতুন ভেরিয়েন্টের জন্য স্টক এন্ট্রি
                    $initialStock = (int) ($variantData['stock'] ?? 0);
                    if ($initialStock > 0) {
                        Stock::updateOrCreate(
                            ['company_id' => $companyId, 'product_id' => $product->id, 'variant_id' => $newVariant->id],
                            ['quantity' => $initialStock, 'reorder_level' => $variantData['reorder_level']]
                        );
                        StockMovement::create([
                            'company_id' => $companyId, 'product_id' => $product->id, 'variant_id' => $newVariant->id,
                            'type' => 'in', 'quantity' => $initialStock, 'reference' => 'New Variant Initial Stock', 'user_id' => $userId,
                        ]);
                    }
                }
            }

            // ৩. যেসব ভেরিয়েন্ট ফর্ম থেকে মুছে ফেলা হয়েছে সেগুলো ডিলিট করা (Optional but recommended)
            // সতর্কতা: যদি পুরানো ভেরিয়েন্টের সেলস হিস্টরি থাকে, তবে হার্ড ডিলিট না করে 'is_active' = false করা ভালো।
            // এখানে আমরা সিম্পলিসিটির জন্য ডিলিট করছি না, শুধু আপডেট/ক্রিয়েট করছি। 

            DB::commit();
            return redirect()->route('company.products.index')->with('success', 'পণ্য সফলভাবে আপডেট করা হয়েছে!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'পণ্য আপডেট করতে সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $this->authorizeCompany($product);

        // চেক করুন প্রোডাক্টের কোনো সেলস বা স্টক মুভমেন্ট হিস্ট্রি আছে কিনা (Best Practice)
        $hasHistory = StockMovement::where('product_id', $product->id)->exists();
        
        if ($hasHistory) {
            return back()->with('error', 'এই পণ্যটি মুছে ফেলা যাবে না কারণ এর লেনদেনের ইতিহাস রয়েছে। আপনি চাইলে এটি Inactive করে দিতে পারেন।');
        }

        DB::beginTransaction();
        try {
            // ভেরিয়েন্ট, স্টক এবং স্টক মুভমেন্ট আগে ডিলিট করতে হবে (Foreign Key Constraint এর কারণে)
            $product->variants()->each(function ($variant) {
                Stock::where('variant_id', $variant->id)->delete();
                StockMovement::where('variant_id', $variant->id)->delete();
                $variant->delete();
            });
            
            $product->delete();
            DB::commit();

            return redirect()->route('company.products.index')->with('success', 'পণ্য সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'পণ্য মুছে ফেলতে সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to ensure the product belongs to the authenticated user's company.
     */
    private function authorizeCompany(Product $product)
    {
        if ($product->company_id !== Auth::user()->company_id) {
            abort(403, 'অননুমোদিত অ্যাক্সেস। এই পণ্যটি আপনার কোম্পানির অন্তর্ভুক্ত নয়।');
        }
    }
}