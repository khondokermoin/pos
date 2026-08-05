<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\ProductVariant;
use Inertia\Inertia;
use Inertia\Response;

/**
 * ShopController — Frontend e-commerce pages (Inertia/React)
 *
 * ARCHITECTURE: Hybrid
 *   - This controller serves the MarketPro React frontend via Inertia::render()
 *   - The backend (Company Admin, Super Admin) remains 100% Blade — untouched
 *
 * TENANT RESOLUTION:
 *   - IdentifyTenantByDomain middleware runs globally on every web request
 *   - It binds the resolved Company model to app('tenant')
 *   - This controller reads app('tenant') to scope all DB queries
 *
 * DATA FLOW:
 *   Inertia::render('MarketPro/ShopPage', $data)
 *     → resources/js/Pages/MarketPro/ShopPage.jsx
 *     → usePage().props.products / usePage().props.categories
 */
class ShopController extends Controller
{
    /**
     * Homepage — featured products, new arrivals, flash sales.
     *
     * The visual design (HomePageOne/Two/Three) is chosen per-tenant based on
     * their business type (see resolveHomeTemplate()), but every design is fed
     * the same real, company-scoped product data — no template gets demo data.
     */
    public function home(): Response
    {
        $tenant = app('tenant');

        $template = $this->resolveHomeTemplate($tenant);

        if (! $tenant) {
            return Inertia::render($template, [
                'featuredProducts' => [],
                'newArrivals'      => [],
                'categories'       => [],
                'brands'           => [],
            ]);
        }

        $featuredProducts = Product::where('company_id', $tenant->id)
            ->where('is_active', true)
            ->with(['category', 'variants' => fn($q) => $q->where('is_active', true)->orderBy('selling_price')])
            ->latest()
            ->take(12)
            ->get()
            ->map(fn($p) => $this->formatProduct($p, $tenant->currency ?? 'BDT'));

        $newArrivals = Product::where('company_id', $tenant->id)
            ->where('is_active', true)
            ->with(['category', 'variants' => fn($q) => $q->where('is_active', true)->orderBy('selling_price')])
            ->latest()
            ->take(16)
            ->get()
            ->map(fn($p) => $this->formatProduct($p, $tenant->currency ?? 'BDT'));

        $categories = Category::where('company_id', $tenant->id)
            ->where('is_active', true)
            ->get(['id', 'name', 'image']);

        $brands = Brand::where('company_id', $tenant->id)
            ->where('is_active', true)
            ->get(['id', 'name']);

        return Inertia::render($template, [
            'featuredProducts' => $featuredProducts,
            'newArrivals'      => $newArrivals,
            'categories'       => $categories,
            'brands'           => $brands,
        ]);
    }

    /**
     * Pick the MarketPro homepage design for a tenant based on their business type.
     *
     * fashion-clothing -> HomePageThree (the fashion-oriented layout)
     * electronics      -> HomePageTwo (gadgets/electronics layout — also covers
     *                      watches, since smart watches are gadgets)
     * everything else  -> HomePageOne (general-purpose default)
     */
    protected function resolveHomeTemplate(?Company $tenant): string
    {
        return match ($tenant?->businessType?->slug) {
            'fashion-clothing' => 'MarketPro/HomePageThree',
            'electronics'      => 'MarketPro/HomePageTwo',
            default            => 'MarketPro/HomePageOne',
        };
    }

    /**
     * Shop listing page — paginated products with category/price filters.
     */
    public function shop(): Response
    {
        $tenant = app('tenant');

        if (! $tenant) {
            return Inertia::render('MarketPro/ShopPage', [
                'products'   => ['data' => [], 'links' => [], 'total' => 0],
                'categories' => [],
                'filters'    => [],
            ]);
        }

        $filters = [
            'category' => request('category'),
            'search'   => request('search'),
            'min'      => request('min'),
            'max'      => request('max'),
            'sort'     => request('sort', 'latest'),
        ];

        $query = Product::where('company_id', $tenant->id)
            ->where('is_active', true)
            ->with(['category', 'variants' => fn($q) => $q->where('is_active', true)->orderBy('selling_price')]);

        if ($filters['category']) {
            $query->where('category_id', $filters['category']);
        }

        if ($filters['search']) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if ($filters['min'] || $filters['max']) {
            $query->whereHas('variants', function ($q) use ($filters) {
                if ($filters['min']) {
                    $q->where('selling_price', '>=', $filters['min']);
                }
                if ($filters['max']) {
                    $q->where('selling_price', '<=', $filters['max']);
                }
            });
        }

        $query = match ($filters['sort']) {
            'price_asc'  => $query->orderByRaw('(SELECT MIN(selling_price) FROM product_variants WHERE product_id = products.id AND is_active = 1) ASC'),
            'price_desc' => $query->orderByRaw('(SELECT MIN(selling_price) FROM product_variants WHERE product_id = products.id AND is_active = 1) DESC'),
            default      => $query->latest(),
        };

        $paginated = $query->paginate(20)->withQueryString();

        $products = [
            'data'  => $paginated->getCollection()->map(fn($p) => $this->formatProduct($p, $tenant->currency ?? 'BDT')),
            'links' => $paginated->linkCollection()->toArray(),
            'meta'  => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'from'         => $paginated->firstItem(),
                'to'           => $paginated->lastItem(),
            ],
        ];

        $categories = Category::where('company_id', $tenant->id)
            ->where('is_active', true)
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->get(['id', 'name', 'image']);

        return Inertia::render('MarketPro/ShopPage', [
            'products'   => $products,
            'categories' => $categories,
            'filters'    => $filters,
        ]);
    }

    /**
     * Product detail page.
     */
    public function productDetail(int $id): Response
    {
        $tenant = app('tenant');

        $product = Product::where('is_active', true)
            ->when($tenant, fn($q) => $q->where('company_id', $tenant->id))
            ->with([
                'category',
                'brand',
                'variants' => fn($q) => $q->where('is_active', true)->with('stock'),
            ])
            ->findOrFail($id);

        $relatedProducts = Product::where('company_id', $product->company_id)
            ->where('category_id', $product->category_id)
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->with(['variants' => fn($q) => $q->where('is_active', true)->orderBy('selling_price')])
            ->take(8)
            ->get()
            ->map(fn($p) => $this->formatProduct($p, $tenant?->currency ?? 'BDT'));

        return Inertia::render('MarketPro/ProductDetailsPageOne', [
            'product'         => $this->formatProductDetail($product, $tenant?->currency ?? 'BDT'),
            'relatedProducts' => $relatedProducts,
        ]);
    }

    /**
     * Checkout page — passes branch routing data so the React form can
     * send city/district/lat/lng with the order submission.
     */
    public function checkout(): Response
    {
        $tenant = app('tenant');

        // Pass branch coverage info to the frontend so the JS can show
        // "We deliver to these areas" messaging and collect location data.
        $branches = [];
        if ($tenant) {
            $branches = \App\Models\Branch::where('company_id', $tenant->id)
                ->where('status', 'active')
                ->where('accepts_online_orders', true)
                ->get(['id', 'name', 'city', 'district', 'coverage_areas', 'is_default'])
                ->map(fn($b) => [
                    'id'             => $b->id,
                    'name'           => $b->name,
                    'city'           => $b->city,
                    'district'       => $b->district,
                    'coverage_areas' => $b->coverage_areas ?? [],
                    'is_default'     => $b->is_default,
                ])
                ->values()
                ->toArray();
        }

        return Inertia::render('MarketPro/CheckoutPage', [
            'branches' => $branches,
        ]);
    }

    /**
     * Cart page — cart state is managed client-side (localStorage/Context).
     * No server data needed.
     */
    public function cart(): Response
    {
        return Inertia::render('MarketPro/CartPage');
    }

    /**
     * Format a product for the React frontend.
     */
    protected function formatProduct(Product $product, string $currency): array
    {
        $primaryVariant = $product->variants->first();

        return [
            'id'            => $product->id,
            'name'          => $product->name,
            'description'   => $product->description,
            'image'         => $product->image ? asset('storage/' . $product->image) : null,
            'category'      => $product->category ? ['id' => $product->category->id, 'name' => $product->category->name] : null,
            'currency'      => $currency,
            'selling_price' => $primaryVariant?->selling_price,
            'cost_price'    => $primaryVariant?->cost_price,
            'sku'           => $primaryVariant?->sku,
            'has_variants'  => $product->has_variants,
            'variants'      => $product->variants->map(fn($v) => [
                'id'            => $v->id,
                'name'          => $v->name,
                'sku'           => $v->sku,
                'selling_price' => $v->selling_price,
                'cost_price'    => $v->cost_price,
                'attributes'    => $v->attributes ?? [],
                'is_active'     => $v->is_active,
            ])->values()->toArray(),
        ];
    }

    /**
     * Format a product with full detail (for product detail page).
     */
    protected function formatProductDetail(Product $product, string $currency): array
    {
        $base = $this->formatProduct($product, $currency);

        $base['brand']    = $product->brand ? ['id' => $product->brand->id, 'name' => $product->brand->name] : null;
        $base['variants'] = $product->variants->map(fn($v) => [
            'id'            => $v->id,
            'name'          => $v->name,
            'sku'           => $v->sku,
            'selling_price' => $v->selling_price,
            'cost_price'    => $v->cost_price,
            'attributes'    => $v->attributes ?? [],
            'is_active'     => $v->is_active,
            'stock'         => $v->stock ? ['quantity' => $v->stock->quantity] : ['quantity' => 0],
        ])->values()->toArray();

        return $base;
    }
}
