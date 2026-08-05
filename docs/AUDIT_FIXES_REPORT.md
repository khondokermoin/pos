# Cloud POS Inventory v5 — Audit Fixes Report

**Date:** 2026-08-06  
**Baseline:** Post Phase 1 (location routing + central warehouse)  
**Status:** All critical and high-severity findings resolved ✅

---

## Summary of Changes

| Severity    | Finding                                            | File(s) Changed                     | Status   |
| ----------- | -------------------------------------------------- | ----------------------------------- | -------- |
| 🔴 Critical | `assignRole()` privilege escalation                | `UserController.php`                | ✅ Fixed |
| 🔴 Critical | SSLCommerz callback unauthenticated login          | `SubscriptionController.php`        | ✅ Fixed |
| 🟠 High     | Company SalesReturn unscoped SaleItem              | `Company/SalesReturnController.php` | ✅ Fixed |
| 🟠 High     | Branch SalesReturn unscoped SaleItem               | `Branch/SalesReturnController.php`  | ✅ Fixed |
| 🟠 High     | PurchaseReturn unscoped PurchaseItem               | `PurchaseReturnController.php`      | ✅ Fixed |
| 🟠 High     | 14 storefront components broken (react-router-dom) | 14 component files                  | ✅ Fixed |
| 🟠 High     | /cart shows hardcoded demo data                    | `CartSection.jsx`                   | ✅ Fixed |
| 🟡 Medium   | TransferController unscoped branch_id/variant_id   | `TransferController.php`            | ✅ Fixed |
| 🟡 Medium   | InventoryController unscoped branch_id/variant_id  | `InventoryController.php`           | ✅ Fixed |
| 🟡 Medium   | QuotationController unscoped variant_id            | `QuotationController.php`           | ✅ Fixed |
| 🟡 Medium   | CSRF exemption too broad (subscribe/\*)            | `bootstrap/app.php`                 | ✅ Fixed |
| 🟡 Medium   | Online storefront checkout missing rate limiting   | `routes/frontend.php`               | ✅ Fixed |
| 🟡 Medium   | HeaderOne cart badge stale (same-tab)              | `HeaderOne.jsx`                     | ✅ Fixed |
| 🟡 Medium   | HeaderOne scroll listener leak on unmount          | `HeaderOne.jsx`                     | ✅ Fixed |
| 🔵 Low      | stocks NULL branch_id uniqueness gap               | New migration                       | ✅ Fixed |
| 🔵 Low      | TransferController branch lookups in loop          | `TransferController.php`            | ✅ Fixed |

---

## Detailed Fix Notes

### 🔴 CRITICAL-1: assignRole() Privilege Escalation

**File:** `app/Http/Controllers/Company/UserController.php:137`

**Before:**

```php
$request->validate([
    'role' => 'required|string',
]);
```

**After:**

```php
$request->validate([
    'role' => 'required|string|in:' . implode(',', $this->allowedRoles),
]);
```

**Why:** Without the `in:` allow-list, any Company Admin could POST `role=Super Admin` to gain unscoped access to all tenant data and the `/super-admin/*` panel. The `$allowedRoles` array (`Manager`, `Salesman`) was already defined and used in `store()` — `assignRole()` simply wasn't using it.

---

### 🔴 CRITICAL-2: SSLCommerz Callback Unauthenticated Login

**File:** `app/Http/Controllers/Company/SubscriptionController.php:595-623`

**Before:** Both the `failed` and `cancelled` paths called `$this->reLoginCompanyAdmin($transaction->company)` — which calls `Auth::login($companyAdmin, remember: true)` — before any payment verification.

**After:** `reLoginCompanyAdmin()` is only called on the **success path** (after `verifySslCommerzIpn()` passes). The failed/cancelled paths redirect to the result page without logging anyone in. The result page is fully public and does not require authentication.

**Why:** The `tran_id` is visible in the browser URL bar, browser history, and the `Referer` header sent to SSLCommerz's hosted page. Anyone who obtained a `tran_id` could visit `/company/subscription/payment/callback?status=cancelled&tran_id=<id>` and receive a persistent `remember: true` authenticated session for that company's admin account — no password required.

---

### 🟠 HIGH-3/4/5: Unscoped Item Lookups in Return Controllers

**Files:**

- `app/Http/Controllers/Company/SalesReturnController.php:89`
- `app/Http/Controllers/Branch/SalesReturnController.php:89`
- `app/Http/Controllers/Company/PurchaseReturnController.php:91`

**Before:**

```php
$saleItem = SaleItem::findOrFail($item['sale_item_id']);
```

**After (Company):**

```php
$saleItem = SaleItem::whereHas(
    'sale',
    fn($q) => $q->where('company_id', $companyId)
)->findOrFail($item['sale_item_id']);
```

**After (Branch):**

```php
$saleItem = SaleItem::whereHas(
    'sale',
    fn($q) => $q->where('branch_id', $branchId)
)->findOrFail($item['sale_item_id']);
```

Also tightened the `sale_id` validation rule to include a company/branch scope:

```php
// Company:
'sale_id' => 'required|exists:sales,id,company_id,' . $companyId,
// Branch:
'sale_id' => 'required|exists:sales,id,branch_id,' . $branchId,
```

**Why:** `SaleItem` and `PurchaseItem` have no `HasCompanyScope` trait. An attacker from Company A could supply a `sale_item_id` belonging to Company B, pulling its `unit_price` and `variant_id` into Company A's financial records.

---

### 🟠 HIGH-6: 14 Storefront Components Broken (react-router-dom)

**Files fixed:**

- `Account.jsx`, `Blog.jsx`, `BlogDetails.jsx`
- `BreadcrumbImage.jsx`, `BreadcrumbThree.jsx`
- `Checkout.jsx`, `Contact.jsx`
- `ProductDetailsTwo.jsx`, `ShopSection.jsx`
- `VendorsList.jsx`, `VendorsListTwo.jsx`
- `VendorTwo.jsx`, `VendorTwoDetails.jsx`
- `WishListSection.jsx`

**Before:**

```jsx
import { Link } from "react-router-dom";
```

**After:**

```jsx
import { Link } from "@inertiajs/react";
```

**Why:** The only `<BrowserRouter>` in the app (`Pages/Welcome.jsx → Pages/MarketPro/App.jsx`) is never rendered by any route — `routes/frontend.php` renders each page directly via Inertia. react-router-dom v7's `Link` calls `useHref()` internally, which throws `"useHref() may be used only in the context of a <Router> component"` outside a Router context, causing a blank page. `HeaderOne.jsx` had already been fixed in Phase 1 with a comment documenting the fix — this propagates that fix to all remaining files.

---

### 🟠 HIGH-7: /cart Shows Hardcoded Demo Data

**File:** `resources/js/Components/MarketPro/CartSection.jsx`

**Before:** Static JSX with 4 hardcoded rows ("Taylor Farms Broccoli Florets", $125.00) — never read from localStorage.

**After:** Full reactive implementation:

- Reads cart from `localStorage['cart']` on mount
- Listens for both `storage` (cross-tab) and `cart:updated` (same-tab) events
- Renders real items with product name, image, price, quantity
- Remove button deletes item and dispatches `cart:updated`
- Quantity changes update localStorage and dispatch `cart:updated`
- Shows empty-state with "Continue Shopping" link when cart is empty
- Subtotal computed from real item prices × quantities

---

### 🟡 MEDIUM-8/9/10: Unscoped Validation in Transfer/Inventory/Quotation

**Files:**

- `app/Http/Controllers/Company/TransferController.php:67`
- `app/Http/Controllers/Company/InventoryController.php:71`
- `app/Http/Controllers/Company/QuotationController.php:67`

**Before:**

```php
'from_branch_id' => 'nullable|exists:branches,id',
'items.*.variant_id' => 'required|exists:product_variants,id',
```

**After:**

```php
'from_branch_id' => 'nullable|exists:branches,id,company_id,' . $companyId,
'to_branch_id'   => 'required|exists:branches,id,company_id,' . $companyId,
'items.*.variant_id' => [
    'required',
    Rule::exists('product_variants', 'id')->whereIn(
        'product_id',
        Product::where('company_id', $companyId)->pluck('id')
    ),
],
```

**Why:** Without company-scoped validation, a Company A admin could supply a `branch_id` or `variant_id` belonging to Company B, creating `Stock`/`StockMovement` rows with cross-tenant foreign keys that corrupt referential integrity.

---

### 🟡 MEDIUM-11: CSRF Exemption Too Broad

**File:** `bootstrap/app.php:30-34`

**Before:**

```php
$middleware->validateCsrfTokens(except: [
    'company/subscription/payment/callback',
    'payment/result',
    'company/subscription/subscribe/*',  // ← WRONG
]);
```

**After:**

```php
$middleware->validateCsrfTokens(except: [
    'company/subscription/payment/callback',
    'payment/result',
    // subscribe/* removed — it's an authenticated user-initiated POST,
    // not a cross-origin gateway callback. CSRF token is present in the form.
]);
```

**Why:** `subscribe/*` is an authenticated POST inside the `auth` middleware group. SSLCommerz never POSTs to it — only the user's browser does. Exempting it opened a CSRF hole where a hostile page could force a billing redirect on a logged-in Company Admin.

---

### 🟡 MEDIUM-12: Online Storefront Checkout Missing Rate Limiting

**File:** `routes/frontend.php:50-51`

**Before:**

```php
Route::post('/order', [OnlineOrderController::class, 'store'])
    ->name('frontend.order.store');
```

**After:**

```php
Route::post('/order', [OnlineOrderController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('frontend.order.store');
```

**Why:** Without rate limiting, a script could repeatedly POST valid orders with `payment_method: cash`, permanently decrementing real stock with each fake order and no payment ever collected. The POS checkout already had `throttle:60,1` from a previous audit round — this applies the same protection to the public storefront endpoint (tighter at 30/min since it's unauthenticated).

---

### 🟡 MEDIUM-13/14: HeaderOne Cart Badge + Scroll Listener

**File:** `resources/js/Components/MarketPro/HeaderOne.jsx`

**Cart badge fix:** Added `window.addEventListener("cart:updated", readCartCount)` alongside the existing `storage` listener. The `storage` event only fires in _other_ tabs; the new `cart:updated` custom event fires in the same tab when any add-to-cart handler calls `window.dispatchEvent(new Event('cart:updated'))` after writing to localStorage.

**Scroll listener fix:** Replaced `window.onscroll = () => { ...; return () => (window.onscroll = null); }` (where the cleanup was dead code inside the callback) with:

```jsx
const handleScroll = () => { ... };
window.addEventListener("scroll", handleScroll);
// cleanup:
return () => { window.removeEventListener("scroll", handleScroll); ... };
```

Using `addEventListener`/`removeEventListener` is also safer than `window.onscroll` because multiple components can coexist without clobbering each other's listener.

---

### 🔵 LOW-15: stocks NULL branch_id Uniqueness Gap

**File:** `database/migrations/2026_08_06_000001_fix_stocks_null_branch_id_uniqueness.php`

**Problem:** Phase 1 made `stocks.branch_id` nullable for central-warehouse stock, but MySQL treats each `NULL` as distinct in a UNIQUE index — so the existing `(company_id, variant_id, branch_id)` unique constraint does NOT prevent two rows with the same `company_id + variant_id` and `branch_id = NULL`.

**Fix:**

1. Merge any existing duplicate central-warehouse rows (sum their quantities)
2. Drop the old unique index
3. Add a generated/virtual column `branch_id_key = COALESCE(branch_id, 0)` (0 for central warehouse, actual branch_id otherwise)
4. Add a new unique index on `(company_id, variant_id, branch_id_key)` — MySQL CAN enforce this since `branch_id_key` is never NULL

**Run:** `php artisan migrate`

---

### 🔵 LOW-16: TransferController Branch Lookups Inside Per-Item Loop

**File:** `app/Http/Controllers/Company/TransferController.php:132-135`

**Before:** `Branch::find($request->to_branch_id)` and `Branch::find($request->from_branch_id)` were called on every iteration of the items loop — up to 2×N redundant queries for an N-item transfer.

**After:** Both branch name lookups moved before the loop:

```php
$toBranchName   = Branch::find($request->to_branch_id)?->name ?? 'Unknown';
$fromBranchName = $request->from_branch_id
    ? (Branch::find($request->from_branch_id)?->name ?? 'Unknown')
    : 'Central Warehouse';

foreach ($request->items as $item) { ... }
```

---

## Remaining Known Issues (Not Fixed in This Pass)

These items were identified in the audit report but are **architectural/tech-debt** items that require more planning rather than a targeted fix:

| Item                                                            | Reason Deferred                                                                                                                                           |
| --------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `app/Policies/*` — 5 policy classes exist but are never invoked | Wiring policies requires a broader refactor of all controllers; no immediate exploit risk since the IDOR bugs above are now fixed at the validation layer |
| `CompanyScope` disabled in console/queue context                | No `app/Jobs` directory exists today; risk is future-facing. Document in CLAUDE.md as a constraint for future job authors                                 |
| Header/Footer/Banner variant duplication (~5600 lines)          | Tech debt, not a security issue; schedule for Phase 2 refactor                                                                                            |
| No `loading="lazy"` on images                                   | Performance improvement; schedule for Phase 2                                                                                                             |
| Dead `react-router-dom` SPA tree (`Welcome.jsx → App.jsx`)      | Can be deleted once the team confirms no other use; low risk since it's never rendered                                                                    |
| `Animation.jsx` imports missing `aos` package                   | Currently unreferenced (never bundled); add `aos` to package.json or delete the file before wiring it in                                                  |

---

## How to Deploy

```bash
# 1. Run the new migration (fixes stocks NULL uniqueness)
php artisan migrate

# 2. Rebuild frontend assets
npm run build

# 3. Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Testing Checklist

After deploying, verify:

- [ ] Company Admin cannot assign `Super Admin` role via PATCH `/company/users/{id}/assign-role`
- [ ] Visiting `/company/subscription/payment/callback?status=cancelled&tran_id=<any>` does NOT log in any user
- [ ] Sales return with a `sale_item_id` from another company returns 404/403
- [ ] Stock transfer with a `to_branch_id` from another company fails validation
- [ ] `/cart` page shows real localStorage cart items (not "Taylor Farms Broccoli Florets")
- [ ] Adding a product to cart updates the header badge immediately (same tab)
- [ ] `/blog`, `/account`, `/vendor`, `/wishlist`, `/contact` pages render without blank screen
- [ ] POST `/order` returns 429 after 30 requests per minute
- [ ] `php artisan migrate` runs without error on the stocks migration
