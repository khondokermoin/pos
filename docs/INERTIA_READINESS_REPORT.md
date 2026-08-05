# 📋 Inertia.js Architecture — Readiness Report

**Project:** Cloud POS Inventory v5  
**Date:** August 2026  
**Architectural Decision:** Strictly Inertia.js — No REST APIs (`/api`). All data flow between Laravel and React must use `Inertia::render()` exclusively.  
**Scope:** Backend Product Management (Add/Edit) + Frontend MarketPro Shop Display

---

## EXECUTIVE SUMMARY

The codebase has a **partially-built Inertia foundation** that is structurally sound but contains two critical disconnections that block the Inertia-only architecture from functioning. The infrastructure layer (middleware, tenant resolution, shared props) is production-ready. The application layer (controllers, routes, React pages) is split between two incompatible paradigms: the backend uses Blade, and the frontend React app uses a client-side router (`react-router-dom`) that bypasses Inertia entirely. Both must be corrected before the Inertia-only architecture can be realized.

---

## SECTION 1 — WHAT IS READY (Existing Assets)

### 1.1 React Component Library — `resources/js/`

The MarketPro React frontend is a **fully-built, visually complete UI kit**. It is not a stub — it is a production-quality storefront with all pages and components implemented.

#### Pages (`resources/js/Pages/MarketPro/`) — 18 Pages

| Page File                   | Route Path             | Purpose                                   |
| --------------------------- | ---------------------- | ----------------------------------------- |
| `HomePageOne.jsx`           | `/`                    | Primary storefront homepage               |
| `HomePageTwo.jsx`           | `/index-two`           | Alternate homepage layout                 |
| `HomePageThree.jsx`         | `/index-three`         | Third homepage variant                    |
| `ShopPage.jsx`              | `/shop`                | **Product listing page — primary target** |
| `ProductDetailsPageOne.jsx` | `/product-details`     | Product detail view (style 1)             |
| `ProductDetailsPageTwo.jsx` | `/product-details-two` | Product detail view (style 2)             |
| `CartPage.jsx`              | `/cart`                | Shopping cart                             |
| `CheckoutPage.jsx`          | `/checkout`            | Checkout flow                             |
| `WishlistPage.jsx`          | `/wishlist`            | Wishlist                                  |
| `AccountPage.jsx`           | `/account`             | Customer account                          |
| `BlogPage.jsx`              | `/blog`                | Blog listing                              |
| `BlogDetailsPage.jsx`       | `/blog-details`        | Blog post detail                          |
| `ContactPage.jsx`           | `/contact`             | Contact page                              |
| `VendorPage.jsx`            | `/vendor`              | Vendor listing                            |
| `VendorDetailsPage.jsx`     | `/vendor-details`      | Vendor detail                             |
| `VendorTwoPage.jsx`         | `/vendor-two`          | Alternate vendor listing                  |
| `VendorTwoDetailsPage.jsx`  | `/vendor-two-details`  | Alternate vendor detail                   |
| `BecomeSellerPage.jsx`      | `/become-seller`       | Seller registration                       |

#### Components (`resources/js/Components/MarketPro/`) — 75 Components

A rich library of 75 pre-built UI components including:

- **Product Display:** `ShopSection.jsx`, `ProductListOne.jsx`, `ProductDetailsOne.jsx`, `ProductDetailsTwo.jsx`, `FlashSalesOne.jsx`, `BestSellsOne.jsx`, `NewArrivalOne.jsx`, `TopSellingOne.jsx`, `HotDealsOne.jsx`
- **Layout:** `HeaderOne.jsx`, `HeaderTwo.jsx`, `HeaderThree.jsx`, `FooterOne.jsx`, `FooterTwo.jsx`, `FooterThree.jsx`, `BottomFooter.jsx`
- **Navigation/UX:** `Breadcrumb.jsx`, `BreadcrumbTwo.jsx`, `BreadcrumbThree.jsx`, `CartSection.jsx`, `WishListSection.jsx`
- **Marketing:** `BannerOne.jsx`, `BannerTwo.jsx`, `BannerThree.jsx`, `PromotionalOne.jsx`, `DealsOne.jsx`, `DiscountOne.jsx`

#### Helpers (`resources/js/Helpers/`) — 6 Helpers

`Preloader.jsx`, `ColorInit.jsx`, `Countdown.js`, `PhosphorIconInit.js`, `QuantityControl.jsx`, `RouteScrollToTop.jsx`

**Assessment:** The React UI layer is **100% structurally complete**. The only thing missing is real data — every component currently renders hardcoded static data (dummy product names, placeholder images, hardcoded prices).

---

### 1.2 Inertia.js Infrastructure — Fully Operational

#### `app/Http/Middleware/HandleInertiaRequests.php`

This middleware is **correctly implemented and globally registered**. It shares four data namespaces with every React page on every request:

```php
// Shares on EVERY Inertia request — available as usePage().props in React
'auth'   => [ user.id, user.name, user.email, user.company_id, user.branch_id, user.roles ]
'tenant' => TenantService::getBrandingData()   // ← The multi-tenant "magic"
'flash'  => [ success, error, warning, info ]  // ← Toast notifications
'app'    => [ name, url, locale, version ]
```

**Critical detail:** The `tenant` prop is a **lazy closure** (`fn() => ...`). It is only evaluated when a React component actually reads `usePage().props.tenant`. This is a performance optimization — it avoids a DB/cache hit on routes that don't need tenant data.

#### `app/Http/Middleware/IdentifyTenantByDomain.php`

This middleware is **correctly implemented and globally registered** in `bootstrap/app.php` as part of the `web` middleware stack. It runs on **every single web request** automatically.

**Resolution flow:**

1. Reads `$request->getHost()` (e.g., `fashionbd.yourpos.com` or `www.mybrand.com`)
2. Calls `TenantService::resolveFromHost($host)`
3. Looks up `companies` table by `custom_domain` OR `subdomain` (with 60-minute cache)
4. Binds the resolved `Company` model to the IoC container as `app('tenant')`
5. Fails gracefully — if resolution fails, the request continues normally (Super Admin routes are unaffected)

#### `app/Services/TenantService.php`

A robust, production-ready service with:

- Two-layer caching (host→ID cache + ID→Company data cache)
- Graceful fallback when cache store is unavailable
- `getBrandingData()` returns a complete branding payload: `id`, `name`, `logo_url`, `favicon_url`, `currency`, `timezone`, `theme` (colors, layout, font), `contact`, `social`
- `clearCache()` for cache invalidation on company update

#### `bootstrap/app.php` — Middleware Registration

Both critical middleware are registered **globally in the `web` stack**:

```php
$middleware->web(append: [
    \App\Http\Middleware\IdentifyTenantByDomain::class,  // ← Runs first
    \App\Http\Middleware\HandleInertiaRequests::class,   // ← Runs second, shares tenant data
]);
```

This means **every frontend route automatically gets tenant branding data** injected into React props without any additional configuration.

#### `resources/js/app.jsx` — Inertia Client Bootstrap

The Inertia client is correctly bootstrapped:

```js
createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob("./Pages/**/*.jsx"),
        ),
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
```

This means any page in `resources/js/Pages/` can be rendered by calling `Inertia::render('PageName', $data)` from any Laravel controller.

#### `vite.config.js` — Build Pipeline

Correctly configured with `@vitejs/plugin-react` and `laravel-vite-plugin`. The single entry point `resources/js/app.jsx` compiles the entire React application.

#### `package.json` — Dependencies

All required Inertia dependencies are present:

- `@inertiajs/react: ^2.0.0` ✅
- `react: ^18.2.0` ✅
- `react-dom: ^18.2.0` ✅
- `laravel-vite-plugin: ^2.0.0` ✅

---

### 1.3 Route Structure — `routes/frontend.php`

The frontend routes file exists and covers all 18 MarketPro pages. The middleware stack is partially correct:

```php
Route::middleware(['web', 'inertia'])->group(function () {
    // 18 routes defined for all MarketPro pages
});
```

---

### 1.4 Backend Data Layer — Production-Ready Models & Controller Logic

The `ProductController.php` contains **battle-hardened business logic** that is worth preserving:

- Company-scoped queries (`WHERE company_id = ?`) on every operation
- Full variant management (create/update/delete variants per product)
- Atomic DB transactions (`DB::beginTransaction()`) for product + variant + stock + stock_movement writes
- `authorizeCompany()` guard preventing cross-tenant data access
- Comprehensive validation with company-scoped `exists:` rules

The `Product` model uses `HasCompanyScope` trait — automatic `WHERE company_id = ?` on all queries.

---

## SECTION 2 — WHAT IS NOT READY (Roadblocks & Gaps)

### 🔴 ROADBLOCK 1 — The Frontend Router Architecture Conflict (Critical)

**This is the most fundamental architectural problem in the entire codebase.**

The MarketPro frontend is built as a **client-side Single Page Application (SPA) using `react-router-dom`**, not as an Inertia.js application. These are two mutually exclusive routing paradigms.

**Evidence — `resources/js/Pages/Welcome.jsx`:**

```jsx
import { BrowserRouter } from "react-router-dom";
import App from "./MarketPro/App";

export default function Welcome() {
    return (
        <BrowserRouter>
            <App /> {/* ← All 18 routes handled by react-router-dom */}
        </BrowserRouter>
    );
}
```

**Evidence — `resources/js/Pages/MarketPro/App.jsx`:**

```jsx
export default function App() {
    return (
        <Routes>
            <Route path="/" element={<HomePageOne />} />
            <Route path="/shop" element={<ShopPage />} />
            {/* ... 16 more client-side routes */}
        </Routes>
    );
}
```

**Evidence — `routes/frontend.php`:**

```php
foreach ($frontendRoutes as $path) {
    Route::get($path, function () {
        return Inertia::render('Welcome');  // ← ALL 18 routes render the SAME 'Welcome' page
    });
}
```

**What this means technically:**

- Laravel serves ONE Inertia page component (`Welcome`) for ALL 18 frontend URLs
- `Welcome.jsx` boots a `BrowserRouter` which then handles all routing client-side
- Inertia's page-level data passing (`Inertia::render('ShopPage', ['products' => $products])`) is **completely bypassed** — there is no `ShopPage` Inertia component, only a `Welcome` component that contains a router
- `usePage().props` in any MarketPro component will only ever contain the shared props from `HandleInertiaRequests` (auth, tenant, flash, app) — **never any page-specific data like products or categories**
- The `react-router-dom` navigation (`<Link to="/shop">`) works entirely in the browser — Laravel never receives a second request when navigating between pages, so no controller can inject data

**The Consequence:** Even if you write a perfect `ShopController` that calls `Inertia::render('MarketPro/ShopPage', ['products' => $products])`, it will **never work** with the current architecture because `ShopPage` is not an Inertia page — it is a component inside a `BrowserRouter` SPA.

---

### 🔴 ROADBLOCK 2 — The Backend Product Management is 100% Blade (Critical)

The "Inertia-only" requirement states that backend Product Management (Add/Edit) must also use Inertia.js and React. Currently, it is entirely Blade-based.

**Evidence — `ProductController.php`:**

```php
// index() — returns Blade view
return view('company.products.index', compact('products'));

// create() — returns Blade view
return view('company.products.create', compact('categories', 'brands', 'units', 'taxes'));

// show() — returns Blade view
return view('company.products.show', compact('product'));

// edit() — returns Blade view
return view('company.products.edit', compact('product', 'categories', 'brands', 'units', 'taxes'));

// store() — redirects (Blade pattern)
return redirect()->route('company.products.index')->with('success', '...');

// update() — redirects (Blade pattern)
return redirect()->route('company.products.index')->with('success', '...');
```

**Evidence — Blade view files exist:**

```
resources/views/company/products/
├── index.blade.php    ← Product listing (Blade)
├── create.blade.php   ← Add product form (Blade)
├── edit.blade.php     ← Edit product form (Blade)
└── show.blade.php     ← Product detail (Blade)
```

**What this means:** To satisfy the "Inertia-only" requirement, all four of these Blade views must be replaced with React/Inertia page components, and the controller must be updated to return `Inertia::render()` instead of `view()`.

---

### 🟠 GAP 3 — No Dedicated Frontend Controller Exists

There is no `FrontendShopController` or equivalent. The `routes/frontend.php` uses anonymous closures that return `Inertia::render('Welcome')` — there is no controller that:

- Reads the resolved tenant from `app('tenant')`
- Queries `Product::where('company_id', $tenant->id)->...`
- Passes product data to a React page via `Inertia::render()`

This controller must be created from scratch.

---

### 🟠 GAP 4 — `routes/frontend.php` Does Not Apply `IdentifyTenantByDomain` Correctly

The `IdentifyTenantByDomain` middleware runs globally on all `web` requests (registered in `bootstrap/app.php`), so tenant resolution does happen. However, `routes/frontend.php` uses `['web', 'inertia']` as its middleware stack:

```php
Route::middleware(['web', 'inertia'])->group(function () { ... });
```

The `'inertia'` alias maps to `HandleInertiaRequests`. Since `HandleInertiaRequests` is **already appended to the global `web` stack**, this creates **double-execution** of the Inertia middleware on frontend routes. This is harmless but redundant and should be cleaned up.

More importantly, the `IdentifyTenantByDomain` middleware is **not explicitly listed** in the frontend route group — it works only because it's in the global stack. This is fine architecturally, but it means the tenant is resolved silently. The new `FrontendShopController` must explicitly read `app('tenant')` to use it.

---

### 🟠 GAP 5 — `ShopSection.jsx` and All Product Components Use 100% Hardcoded Static Data

Every product component renders dummy data with no data-binding mechanism:

```jsx
// ShopSection.jsx — hardcoded product cards
<h6>Taylor Farms Broccoli Florets Vegetables</h6>
<span>$14.99</span>
<img src="/assets/images/thumbs/product-two-img1.png" />

// ShopSection.jsx — hardcoded categories
<Link to="/product-details-two">Mobile & Accessories (12)</Link>
<Link to="/product-details-two">Laptop (12)</Link>
```

No component uses `usePage().props` to read Inertia-injected data. No component accepts `products` or `categories` as props. Every component must be refactored to accept and render dynamic data.

---

### 🟡 GAP 6 — `react-router-dom` `<Link>` vs Inertia `<Link>` Conflict

All 75 MarketPro components use `<Link>` from `react-router-dom`:

```jsx
import { Link } from "react-router-dom";
```

In a pure Inertia architecture, navigation between pages should use `<Link>` from `@inertiajs/react`:

```jsx
import { Link } from "@inertiajs/react";
```

The `react-router-dom` `<Link>` performs client-side navigation without hitting the server, which means no new Inertia request is made, and no new data is injected. This must be replaced in components where server-side data is needed on navigation.

---

### 🟡 GAP 7 — No `usePage()` Hook Usage Anywhere in MarketPro Components

A search across all 75 MarketPro components and 18 MarketPro pages reveals **zero usage** of `usePage()` from `@inertiajs/react`. This confirms that the entire frontend is currently operating as a pure client-side SPA with no Inertia data integration whatsoever.

---

## SECTION 3 — STEP-BY-STEP ACTION PLAN

### Strategic Decision: Which to Build First?

**Answer: Connect the Frontend Shop Page to Laravel controllers FIRST.**

**Rationale:**

1. The frontend Shop page is the **customer-facing, revenue-generating** feature. It is the primary deliverable of the Inertia-only architecture.
2. Converting the backend Product Management from Blade to Inertia is a **larger, more complex refactor** (4 Blade views → 4 React pages + complex form with variant management). It carries higher risk.
3. The frontend connection can be achieved with **surgical, low-risk changes**: one new controller, one route update, and props injection into existing components.
4. Once the frontend data pipeline is proven working (Inertia → React → real products), the same pattern is simply replicated for the backend management pages.
5. The backend Blade views **continue to work perfectly** during the transition — there is zero downtime or regression risk.

---

### Phase 1 — Fix the Frontend Architecture (Highest Priority)

#### Step 1.1 — Create a Dedicated `FrontendController`

Create `app/Http/Controllers/Frontend/ShopController.php`:

```php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Inertia\Inertia;

class ShopController extends Controller
{
    public function shop()
    {
        // Tenant is already resolved by IdentifyTenantByDomain middleware
        $tenant = app('tenant');

        if (! $tenant) {
            // No tenant resolved — show empty shop or 404
            return Inertia::render('MarketPro/ShopPage', [
                'products'   => [],
                'categories' => [],
                'tenant'     => null,
            ]);
        }

        $products = Product::where('company_id', $tenant->id)
            ->where('is_active', true)
            ->with(['category', 'variants' => fn($q) => $q->where('selling_price', '>', 0)])
            ->latest()
            ->paginate(20);

        $categories = Category::where('company_id', $tenant->id)
            ->where('is_active', true)
            ->get(['id', 'name']);

        return Inertia::render('MarketPro/ShopPage', [
            'products'   => $products,
            'categories' => $categories,
        ]);
    }

    public function home()
    {
        $tenant = app('tenant');
        // ... similar pattern for homepage featured products
        return Inertia::render('MarketPro/HomePageOne', [
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
```

**Key principle:** `Inertia::render('MarketPro/ShopPage', [...])` maps directly to `resources/js/Pages/MarketPro/ShopPage.jsx`. The second argument becomes `usePage().props` in React.

---

#### Step 1.2 — Refactor `routes/frontend.php`

Replace the anonymous closure loop with named controller routes:

```php
<?php
use App\Http\Controllers\Frontend\ShopController;
use Inertia\Inertia;

// Frontend routes — IdentifyTenantByDomain runs globally, no need to add it here
Route::middleware('web')->group(function () {

    // ── Tenant-aware routes (serve real data) ──────────────────────────────
    Route::get('/',     [ShopController::class, 'home'])->name('frontend.home');
    Route::get('/shop', [ShopController::class, 'shop'])->name('frontend.shop');
    Route::get('/product/{id}', [ShopController::class, 'productDetail'])->name('frontend.product');

    // ── Static/UI-only routes (no DB data needed yet) ─────────────────────
    Route::get('/cart',      fn() => Inertia::render('MarketPro/CartPage'))->name('frontend.cart');
    Route::get('/checkout',  fn() => Inertia::render('MarketPro/CheckoutPage'))->name('frontend.checkout');
    Route::get('/wishlist',  fn() => Inertia::render('MarketPro/WishlistPage'))->name('frontend.wishlist');
    Route::get('/contact',   fn() => Inertia::render('MarketPro/ContactPage'))->name('frontend.contact');
    // ... remaining static routes
});
```

**Critical change:** Each route now renders its **own dedicated Inertia page** (e.g., `'MarketPro/ShopPage'`), not the generic `'Welcome'` wrapper. This is what enables per-page data injection.

---

#### Step 1.3 — Dissolve the `BrowserRouter` SPA Wrapper

**This is the most architecturally significant change.**

The `Welcome.jsx` file and `App.jsx` router must be eliminated. Each MarketPro page becomes a standalone Inertia page component.

**Before (current — SPA pattern):**

```
Laravel → Inertia::render('Welcome') → Welcome.jsx → BrowserRouter → App.jsx → <Routes> → ShopPage.jsx
```

**After (target — Inertia pattern):**

```
Laravel → Inertia::render('MarketPro/ShopPage', $data) → ShopPage.jsx (reads usePage().props)
```

**Action:** `resources/js/Pages/Welcome.jsx` can be repurposed or deleted. `resources/js/Pages/MarketPro/App.jsx` is no longer needed as a router — it can be deleted.

Each page like `ShopPage.jsx` becomes a self-contained Inertia page:

```jsx
// resources/js/Pages/MarketPro/ShopPage.jsx — AFTER refactor
import { usePage } from "@inertiajs/react";
import ShopSection from "../../Components/MarketPro/ShopSection";
// ... other imports

const ShopPage = () => {
    const { products, categories } = usePage().props;

    return (
        <>
            <HeaderTwo category={true} />
            <Breadcrumb title={"Shop"} />
            <ShopSection products={products} categories={categories} />
            <FooterTwo />
        </>
    );
};
export default ShopPage;
```

---

#### Step 1.4 — Refactor `ShopSection.jsx` to Accept Props

Replace all hardcoded data with dynamic props:

```jsx
// resources/js/Components/MarketPro/ShopSection.jsx — AFTER refactor
const ShopSection = ({ products, categories }) => {
    const { tenant } = usePage().props; // Branding from HandleInertiaRequests

    return (
        <section className="shop py-80">
            {/* Sidebar — dynamic categories */}
            <ul>
                {categories.map((cat) => (
                    <li key={cat.id}>
                        <Link href={`/shop?category=${cat.id}`}>
                            {cat.name}
                        </Link>
                    </li>
                ))}
            </ul>

            {/* Product grid — dynamic products */}
            <div className="list-grid-wrapper">
                {products.data.map((product) => (
                    <ProductCard
                        key={product.id}
                        product={product}
                        currency={tenant?.currency}
                    />
                ))}
            </div>

            {/* Inertia pagination */}
            <Pagination links={products.links} />
        </section>
    );
};
```

**Note:** Replace `<Link>` from `react-router-dom` with `<Link>` from `@inertiajs/react` for all navigation that requires server-side data.

---

### Phase 2 — Convert Backend Product Management to Inertia/React

This phase converts the 4 Blade views to React/Inertia pages. The controller business logic (validation, DB transactions, stock management) is **preserved entirely** — only the return statements change.

#### Step 2.1 — Update `ProductController.php` Return Statements

```php
// BEFORE (Blade):
public function index() {
    return view('company.products.index', compact('products'));
}

// AFTER (Inertia):
public function index() {
    $companyId = Auth::user()->company_id;
    $products = Product::where('company_id', $companyId)
        ->with(['category', 'brand', 'variants.stock'])
        ->latest()
        ->paginate(15);

    return Inertia::render('Company/Products/Index', [
        'products' => $products,
    ]);
}
```

```php
// BEFORE (Blade):
public function create() {
    return view('company.products.create', compact('categories', 'brands', 'units', 'taxes'));
}

// AFTER (Inertia):
public function create() {
    return Inertia::render('Company/Products/Create', [
        'categories' => $categories,
        'brands'     => $brands,
        'units'      => $units,
        'taxes'      => $taxes,
    ]);
}
```

```php
// store() and update() — change redirect pattern to Inertia redirect:
// BEFORE:
return redirect()->route('company.products.index')->with('success', '...');

// AFTER (Inertia-compatible — flash is already shared via HandleInertiaRequests):
return redirect()->route('company.products.index')->with('success', '...');
// ✅ No change needed — Inertia handles flash messages via the shared 'flash' prop
```

#### Step 2.2 — Create React Page Components for Backend Management

Create the following new React pages:

```
resources/js/Pages/Company/Products/
├── Index.jsx    ← Product listing table with pagination
├── Create.jsx   ← Add product form with variant management
├── Edit.jsx     ← Edit product form (pre-populated)
└── Show.jsx     ← Product detail view
```

**`Create.jsx` / `Edit.jsx` complexity note:** The variant management form (dynamic add/remove variants, attribute key-value pairs) is the most complex UI in this conversion. It requires `useForm()` from `@inertiajs/react` for form state management and submission:

```jsx
import { useForm } from "@inertiajs/react";

const Create = ({ categories, brands, units, taxes }) => {
    const { data, setData, post, processing, errors } = useForm({
        name: "",
        category_id: "",
        variants: [
            {
                sku: "",
                selling_price: "",
                cost_price: "",
                stock: 0,
                unit_id: "",
                attributes: [],
            },
        ],
    });

    const submit = (e) => {
        e.preventDefault();
        post(route("company.products.store"));
    };
    // ...
};
```

#### Step 2.3 — Retire the Blade Product Views

Once the React pages are verified working, the Blade files can be archived:

```
resources/views/company/products/
├── index.blade.php    → DELETE (replaced by Pages/Company/Products/Index.jsx)
├── create.blade.php   → DELETE (replaced by Pages/Company/Products/Create.jsx)
├── edit.blade.php     → DELETE (replaced by Pages/Company/Products/Edit.jsx)
└── show.blade.php     → DELETE (replaced by Pages/Company/Products/Show.jsx)
```

---

### Phase 3 — Tenant Branding Integration in React

Once data flows correctly, wire the tenant branding into the React UI. The `tenant` prop is already available on every page via `HandleInertiaRequests`:

```jsx
// In any MarketPro component:
import { usePage } from "@inertiajs/react";

const HeaderOne = () => {
    const { tenant } = usePage().props;

    return (
        <header>
            {tenant?.logo_url && (
                <img src={tenant.logo_url} alt={tenant.name} />
            )}
            <span>{tenant?.name}</span>
        </header>
    );
};
```

This is the "white-label" feature — each tenant's storefront automatically shows their own logo, colors, and company name.

---

## SECTION 4 — ARCHITECTURE DIAGRAM (Before vs. After)

### Current State (Broken SPA Architecture)

```
Browser: GET /shop
    │
    ▼
Laravel Router (routes/frontend.php)
    │  Route::get('/shop', fn() => Inertia::render('Welcome'))
    │
    ▼
Inertia::render('Welcome')
    │  props: { auth, tenant, flash, app }  ← Only shared props, NO product data
    │
    ▼
resources/js/Pages/Welcome.jsx
    │  <BrowserRouter>
    │      <App />   ← react-router-dom takes over
    │  </BrowserRouter>
    │
    ▼
resources/js/Pages/MarketPro/App.jsx
    │  <Route path="/shop" element={<ShopPage />} />
    │
    ▼
resources/js/Pages/MarketPro/ShopPage.jsx
    │  <ShopSection />   ← Renders HARDCODED static data
    │                       NO connection to Laravel DB
    ▼
Browser renders dummy products
```

### Target State (Inertia-Only Architecture)

```
Browser: GET /shop (tenant: fashionbd.yourpos.com)
    │
    ▼
IdentifyTenantByDomain middleware
    │  Resolves Company{id:5, name:"Fashion BD"} → app('tenant')
    │
    ▼
Laravel Router (routes/frontend.php)
    │  Route::get('/shop', [ShopController::class, 'shop'])
    │
    ▼
ShopController::shop()
    │  $tenant = app('tenant')  → Company{id:5}
    │  $products = Product::where('company_id', 5)->paginate(20)
    │  $categories = Category::where('company_id', 5)->get()
    │
    ▼
Inertia::render('MarketPro/ShopPage', [
    │  'products'   => $products,    ← Real DB data
    │  'categories' => $categories,  ← Real DB data
    │  // + shared: auth, tenant{logo,colors,...}, flash, app
    │])
    │
    ▼
resources/js/Pages/MarketPro/ShopPage.jsx
    │  const { products, categories } = usePage().props;
    │  <ShopSection products={products} categories={categories} />
    │
    ▼
Browser renders Fashion BD's real products with their branding
```

---

## SECTION 5 — RISK ASSESSMENT & DEPENDENCY MAP

| Risk                                                           | Severity      | Mitigation                                                                                                                     |
| -------------------------------------------------------------- | ------------- | ------------------------------------------------------------------------------------------------------------------------------ |
| Removing `BrowserRouter` breaks all client-side navigation     | 🔴 High       | Replace `react-router-dom` `<Link>` with `@inertiajs/react` `<Link>` in all components that navigate between data-driven pages |
| Variant management form is complex to rebuild in React         | 🟠 Medium     | Use `useForm()` from `@inertiajs/react`; preserve all validation logic in the controller                                       |
| Inertia pagination differs from Blade pagination               | 🟡 Low        | Use `products.links` from Laravel's paginator; build a simple `<Pagination>` React component                                   |
| `react-router-dom` still needed for cart/wishlist client state | 🟡 Low        | Cart/wishlist can remain client-side state (localStorage/Context); only data-fetching routes need Inertia navigation           |
| Double middleware execution on frontend routes                 | 🟢 Negligible | Remove `'inertia'` alias from `routes/frontend.php` middleware group                                                           |

---

## SECTION 6 — SUMMARY CHECKLIST

### ✅ Already Done (No Action Required)

- [x] `HandleInertiaRequests` middleware — implemented and globally registered
- [x] `IdentifyTenantByDomain` middleware — implemented and globally registered
- [x] `TenantService` with branding data — fully implemented
- [x] Inertia client bootstrap (`app.jsx`) — correctly configured
- [x] Vite + React build pipeline — correctly configured
- [x] All 18 MarketPro page components — structurally complete
- [x] All 75 MarketPro UI components — structurally complete
- [x] `ProductController` business logic — production-ready (preserve entirely)
- [x] `Product` model with `HasCompanyScope` — correctly implemented
- [x] `@inertiajs/react` package installed — v2.0.0

### ❌ Must Be Built (Action Required)

- [ ] **[Phase 1, Step 1]** Create `app/Http/Controllers/Frontend/ShopController.php`
- [ ] **[Phase 1, Step 2]** Refactor `routes/frontend.php` — replace closure loop with controller routes, one route per page
- [ ] **[Phase 1, Step 3]** Delete `resources/js/Pages/Welcome.jsx` SPA wrapper and `App.jsx` router
- [ ] **[Phase 1, Step 4]** Refactor `ShopPage.jsx` — add `usePage().props` destructuring, pass props to `ShopSection`
- [ ] **[Phase 1, Step 5]** Refactor `ShopSection.jsx` — replace hardcoded data with dynamic props
- [ ] **[Phase 1, Step 6]** Replace `react-router-dom` `<Link>` with `@inertiajs/react` `<Link>` in navigating components
- [ ] **[Phase 2, Step 1]** Update `ProductController.php` — change `view()` to `Inertia::render()` in all 4 methods
- [ ] **[Phase 2, Step 2]** Create `resources/js/Pages/Company/Products/Index.jsx`
- [ ] **[Phase 2, Step 3]** Create `resources/js/Pages/Company/Products/Create.jsx` (with `useForm()`)
- [ ] **[Phase 2, Step 4]** Create `resources/js/Pages/Company/Products/Edit.jsx` (with `useForm()`, pre-populated)
- [ ] **[Phase 2, Step 5]** Create `resources/js/Pages/Company/Products/Show.jsx`
- [ ] **[Phase 2, Step 6]** Delete the 4 Blade product views after React pages are verified
- [ ] **[Phase 3]** Wire `tenant` branding props into `HeaderOne/Two/Three.jsx` and `FooterOne/Two/Three.jsx`

---

_Report generated by automated codebase analysis — August 2026_
