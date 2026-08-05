# 🔍 CLOUD POS INVENTORY — FULL PROJECT AUDIT REPORT

**Prepared for:** Lead Engineer / Development Team  
**Project:** Cloud POS Inventory v5  
**Stack:** Laravel 12 + Blade + Inertia/React (hybrid) + Tailwind CSS + MySQL  
**Audit Date:** August 2026  
**Auditor:** AI Code Analyst (Cline)  
**Last Updated:** August 2026 — All Critical + High priority items resolved ✅

---

## ✅ FIX SUMMARY (Applied by Engineer — August 2026)

All **5 Critical** and **9 High** priority bugs have been fixed and verified lint-clean.

| #   | Fix Applied                                                                         | Files Changed                                                                  |
| --- | ----------------------------------------------------------------------------------- | ------------------------------------------------------------------------------ |
| 1   | `CURLOPT_SSL_VERIFYPEER` now `!$isSandbox` (not hardcoded `false`)                  | `SubscriptionController.php`                                                   |
| 2   | Balance Sheet now computes real totals from `CashAccount`, `Asset`, `Loan`          | `ReportController.php`                                                         |
| 3   | P&L now uses actual COGS (`sale_items.quantity × product_variants.cost_price`)      | `ReportController.php`                                                         |
| 4   | `PurchaseController::destroy()` now reverses stock + logs adjustment movement       | `PurchaseController.php`                                                       |
| 5   | Duplicate `/profile` route block removed (verified via `route:list`)                | `routes/web.php`                                                               |
| 6   | `status='active'` → `is_active=true` for Category/Brand/Unit/Tax in `edit()`        | `ProductController.php`                                                        |
| 7   | Quotation number generation uses `lockForUpdate()` to serialize concurrent requests | `QuotationController.php`                                                      |
| 8   | Employee increment validation: `exists:employees,id,company_id,{id}` added          | `EmployeeController.php`                                                       |
| 9   | Loan payment validation: `exists:loans,id,company_id,{id}` added                    | `LoanController.php`                                                           |
| 10  | Cash transfer validation: account ownership check added to both account IDs         | `CashBookController.php`                                                       |
| 11  | Debug payment logs gated behind `config('app.debug')`                               | `SubscriptionController.php`                                                   |
| 12  | `env('SUPER_ADMIN_EMAIL')` replaced with `config('app.super_admin_email')`          | `SubscriptionController.php`, `config/app.php`                                 |
| 13  | Sales/Purchase Return `$item->price` → `$item->unit_price` (column name fix)        | `SalesReturnController.php`, `PurchaseReturnController.php` (Company + Branch) |

### 📝 Audit Corrections (Stubs that were already implemented)

- **STUB-008 / STUB-009** were incorrect — Sales Return and Purchase Return controllers **already** restore/reduce stock. The real bug was a wrong column name (`price` vs `unit_price`) silently zeroing return amounts. This has been fixed.

### ⚠️ Pre-existing Schema Issue (Out of Scope — Flagged for Awareness)

- **Central-warehouse purchases** (`branch_id = NULL`) will hit a `NOT NULL` constraint on `stock_movements.branch_id`. This is a pre-existing schema design issue unrelated to the fixes above. **Fix needed:** Add a migration to make `stock_movements.branch_id` nullable.

---

## 📋 TABLE OF CONTENTS

1. [Project Overview](#1-project-overview)
2. [Architecture Summary](#2-architecture-summary)
3. [What Is Working Well ✅](#3-what-is-working-well)
4. [Bugs & Critical Issues 🐛](#4-bugs--critical-issues)
5. [Incomplete / Stub Features ⚠️](#5-incomplete--stub-features)
6. [Security Vulnerabilities 🔒](#6-security-vulnerabilities)
7. [Performance Issues 🚀](#7-performance-issues)
8. [Code Quality Issues 🧹](#8-code-quality-issues)
9. [Missing Features (Planned but Not Built) 📌](#9-missing-features-planned-but-not-built)
10. [Testing Coverage 🧪](#10-testing-coverage)
11. [DevOps / Deployment Issues 🐳](#11-devops--deployment-issues)
12. [Priority Action Plan 📅](#12-priority-action-plan)

---

## 1. PROJECT OVERVIEW

This is a **multi-tenant SaaS POS + ERP system** built on Laravel 12. It supports three user tiers:

| Role               | Panel                | Prefix         |
| ------------------ | -------------------- | -------------- |
| Super Admin        | SaaS Owner Dashboard | `/super-admin` |
| Company Admin      | Shop Owner Dashboard | `/company`     |
| Manager / Salesman | Branch POS Terminal  | `/branch`      |

**Key Modules Implemented:**

- POS Terminal (Branch level)
- Inventory Management (Company + Branch)
- Purchase & Sales Management
- ERP Expansion: Quotations, Sales Returns, Purchase Returns
- Cash Book, Loan Management, Asset Management
- HR: Departments, Employees, Payroll
- SaaS Billing: Plans, Subscriptions, SSLCommerz Payment Gateway
- Super Admin: Company Management, Impersonation, System Backup, Activity Logs
- Email Templates, Invoice Templates, Barcode Settings
- Addon Marketplace (UI only)

---

## 2. ARCHITECTURE SUMMARY

```
app/
├── Http/Controllers/
│   ├── SuperAdmin/     (22 controllers)
│   ├── Company/        (22 controllers)
│   ├── Branch/         (12 controllers)
│   └── Tenant/         (1 controller — AttributeController)
├── Models/             (55+ models)
├── Services/           (3 services)
├── Middleware/         (5 middleware)
├── Traits/             (LogActivity, HasCompanyScope)
└── Policies/           (exists but not audited)

resources/
├── views/              (Blade — primary UI)
│   ├── super-admin/
│   ├── company/
│   └── branch/
└── js/Pages/           (Inertia/React — only used for Welcome + Auth + MarketPro)

database/
├── migrations/         (50+ migration files)
└── seeders/            (15+ seeders)
```

**Architecture Pattern:** Single-database multi-tenancy using `company_id` scoping on all tenant models. No separate databases per tenant.

---

## 3. WHAT IS WORKING WELL ✅

### 3.1 POS Terminal (Branch)

- ✅ Full POS checkout with server-side price validation (client price manipulation is blocked)
- ✅ Atomic DB transactions with `lockForUpdate()` to prevent race conditions on stock
- ✅ Barcode/SKU/name search with priority ordering
- ✅ Hold/Suspend orders feature
- ✅ Quick customer creation from POS
- ✅ Shift management (open/close cash register)
- ✅ Invoice print view with template support

### 3.2 SaaS Billing

- ✅ SSLCommerz payment gateway integration (sandbox + live)
- ✅ Subscription renewal logic (extend existing vs. create new)
- ✅ Downgrade exploit guard (blocks downgrade if usage exceeds plan limits)
- ✅ Idempotency guard (prevents double-processing of same transaction)
- ✅ Session recovery after SSLCommerz cross-origin POST callback
- ✅ PDF invoice generation and email notification on payment success
- ✅ Subscription middleware with redirect-loop prevention

### 3.3 Security

- ✅ Company-scoped authorization on all tenant models (`authorizeCompany()`)
- ✅ `HasCompanyScope` trait for automatic query scoping
- ✅ Role-based access control via Spatie Laravel Permission
- ✅ Impersonation system (Super Admin → Company → Branch)
- ✅ CSRF protection on all forms
- ✅ Input validation on all store/update methods

### 3.4 Tenant Provisioning

- ✅ Automatic setup on company creation: units, taxes, categories, walk-in customer, attributes, modules
- ✅ Business-type-specific default attributes (Fashion, Electronics, Restaurant, Retail)
- ✅ Welcome email on provisioning

### 3.5 Activity Logging

- ✅ `LogActivity` trait auto-logs created/updated/deleted events on key models
- ✅ Skips logging during console/seeder runs
- ✅ Supports `$logOnly` and `$logExclude` per model

### 3.6 System Tools (Super Admin)

- ✅ Database backup (mysqldump + PHP fallback)
- ✅ Cache clear, config clear, view clear, route clear
- ✅ System info page (PHP version, extensions, disk usage)
- ✅ Activity log viewer with filters

---

## 4. BUGS & CRITICAL ISSUES 🐛

### BUG-001 — Duplicate Profile Routes (HIGH)

**File:** `routes/web.php` — Lines 96-100 and Lines 417-421  
**Problem:** The `/profile` routes are registered **twice** — once inside `Route::middleware('auth')` at line 96 and again at line 417. This causes a duplicate route warning and the second registration silently overrides the first.  
**Fix:** Remove the duplicate block at lines 417-421.

```php
// REMOVE THIS DUPLICATE BLOCK (lines 417-421):
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
```

---

### BUG-002 — Balance Sheet Returns Hardcoded Zeros (HIGH)

**File:** `app/Http/Controllers/Company/ReportController.php` — Lines 64-74  
**Problem:** The `balanceSheet()` method returns `$totalAssets = 0`, `$totalLiabilities = 0`, `$equity = 0` hardcoded. The comment says "Placeholder totals — replace with real accounting data." This means the Balance Sheet report is completely non-functional and shows fake data to users.  
**Fix:** Implement real calculations using `CashAccount`, `Asset`, and `Loan` models which already exist in the database.

```php
// CURRENT (broken):
$totalAssets      = 0;
$totalLiabilities = 0;
$equity           = $totalAssets - $totalLiabilities;

// SHOULD BE:
$totalAssets = CashAccount::where('company_id', $companyId)->sum('current_balance')
             + Asset::where('company_id', $companyId)->where('status', 'active')->sum('current_value');
$totalLiabilities = Loan::where('company_id', $companyId)->where('status', 'active')->sum('amount');
$equity = $totalAssets - $totalLiabilities;
```

---

### BUG-003 — Profit & Loss Calculation is Incorrect (HIGH)

**File:** `app/Http/Controllers/Company/ReportController.php` — Lines 97-98  
**Problem:** `$grossProfit = $totalRevenue - $totalPurchases` is wrong. This subtracts ALL purchases from revenue, but purchases include items that are still in stock (not yet sold). The correct formula is `Revenue - Cost of Goods Sold (COGS)`. COGS = sum of `cost_price × quantity` from `sale_items` joined with `product_variants`.  
**Fix:** Calculate COGS from actual sale items, not total purchase amount.

---

### BUG-004 — Supplier Payable Report Logic is Wrong (MEDIUM)

**File:** `app/Http/Controllers/Company/ReportController.php` — Lines 148-158  
**Problem:** `balance_due = total_purchased - total_paid` where `total_paid` is filtered by `status = 'completed'`. This means if a purchase has `status = 'pending'`, it's counted in `total_purchased` but NOT in `total_paid`, making the balance appear higher than it actually is. The logic conflates purchase status with payment status.  
**Fix:** Add a `paid_amount` column to the `purchases` table, or use a separate `payments` table for supplier payments.

---

### BUG-005 — Quotation Number Can Duplicate (MEDIUM)

**File:** `app/Http/Controllers/Company/QuotationController.php` — Lines 72-73  
**Problem:** Quotation number is generated as:

```php
$lastNo = Quotation::where('company_id', $companyId)->count() + 1;
$quotationNo = 'QT-' . date('Ymd') . '-' . str_pad($lastNo, 4, '0', STR_PAD_LEFT);
```

If two users create quotations simultaneously, `count()` returns the same value for both, causing a **duplicate quotation number** and a unique constraint violation crash.  
**Fix:** Use `DB::table('quotations')->lockForUpdate()->max('id') + 1` inside the transaction, or use a dedicated sequence table.

---

### BUG-006 — Purchase Destroy Does NOT Reverse Stock (HIGH)

**File:** `app/Http/Controllers/Company/PurchaseController.php` — Lines 151-168  
**Problem:** When a purchase is deleted, the code deletes the purchase record but **does NOT reverse the stock quantity** that was added when the purchase was created. This causes phantom stock — items appear in inventory that were never actually received.  
**Fix:** Before deleting, loop through `$purchase->items` and decrement stock + create a `stock_movement` of type `purchase_return`.

---

### BUG-007 — Employee Salary Increment Does Not Validate Company Ownership (MEDIUM)

**File:** `app/Http/Controllers/Company/EmployeeController.php` — Lines 138-156  
**Problem:** In `storeIncrement()`, the validation checks `exists:employees,id` but does NOT verify that the employee belongs to the authenticated user's company. A malicious Company Admin could submit another company's `employee_id` and increment their salary.  
**Fix:** Change validation to `exists:employees,id,company_id,{$this->companyId()}`.

---

### BUG-008 — Loan Payment Does Not Validate Loan Ownership (MEDIUM)

**File:** `app/Http/Controllers/Company/LoanController.php` — Lines 118-133  
**Problem:** `storePayment()` validates `exists:loans,id` but does NOT check that the loan belongs to the current company. A Company Admin could record payments against another company's loans.  
**Fix:** Add company scope check: `exists:loans,id,company_id,{$this->companyId()}`.

---

### BUG-009 — Cash Transfer Does Not Validate Account Ownership (MEDIUM)

**File:** `app/Http/Controllers/Company/CashBookController.php` — Lines 87-93  
**Problem:** The validation for `from_account_id` and `to_account_id` only checks `exists:cash_accounts,id` without verifying the accounts belong to the current company. A malicious user could transfer money between accounts of different companies.  
**Fix:** Add `exists:cash_accounts,id,company_id,{$this->companyId()}` to both validations.

---

### BUG-010 — `SSL_VERIFYPEER` Disabled in Production (HIGH - SECURITY)

**File:** `app/Http/Controllers/Company/SubscriptionController.php` — Line 254  
**Problem:** `curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false)` is set unconditionally. The comment says "sandbox এর জন্য" (for sandbox), but this runs in production too. Disabling SSL peer verification makes the payment gateway connection vulnerable to **Man-in-the-Middle (MITM) attacks**.  
**Fix:** Only disable in sandbox mode:

```php
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, !$isSandbox);
```

---

## 5. INCOMPLETE / STUB FEATURES ⚠️

### STUB-001 — Addon Marketplace is UI-Only

**File:** `app/Http/Controllers/SuperAdmin/AddonMarketplaceController.php`  
**Status:** The marketplace page exists but addons cannot actually be installed, activated, or deactivated. The `Addon` model exists but there is no integration with any real addon system.  
**Work Needed:** Define addon lifecycle (install → activate → deactivate → uninstall), create addon hooks/events system.

---

### STUB-002 — System Update Feature is Incomplete

**File:** `app/Http/Controllers/SuperAdmin/SystemController.php` — `runUpdate()` method  
**Status:** The "update" feature only runs `php artisan migrate` and clears caches. There is no version checking, no git pull, no package update, no rollback mechanism.  
**Work Needed:** Implement proper update pipeline or remove the feature to avoid false expectations.

---

### STUB-003 — MarketPro Frontend (React) is Disconnected

**Files:** `resources/js/Pages/MarketPro/` (20+ React components)  
**Status:** There is a complete React-based e-commerce frontend (HomePageOne, ShopPage, CartPage, CheckoutPage, etc.) that appears to be a separate product/template. It is NOT connected to the POS backend — no API routes, no product data, no cart logic.  
**Work Needed:** Either (a) connect it to the backend with proper API endpoints, or (b) remove it to reduce bundle size and confusion.

---

### STUB-004 — `app/Http/Controllers/Tenant/AttributeController.php` is Isolated

**Status:** This controller exists in a `Tenant/` namespace but is only referenced once in routes (under company settings). It is not part of a broader "Tenant" architecture. The `resources/views/tenant/` directory only has `settings/attributes/` views.  
**Work Needed:** Either expand the Tenant namespace into a proper architecture or merge it into the Company namespace.

---

### STUB-005 — Payroll Has No Bonus/Deduction UI

**File:** `app/Http/Controllers/Company/PayrollController.php`  
**Status:** The `payrolls` table has `bonus` and `deduction` columns, but `generate()` always sets them to `0`. There is no UI to edit individual payroll entries to add bonuses or deductions before marking as paid.  
**Work Needed:** Add an "Edit Payroll" form that allows adjusting bonus and deduction per employee per month, and recalculates `net_salary`.

---

### STUB-006 — Asset Depreciation Not Implemented

**File:** `app/Http/Controllers/Company/AssetController.php`  
**Status:** Assets have `purchase_price` and `current_value` columns, but there is no depreciation calculation, no depreciation schedule, and `current_value` is never automatically updated.  
**Work Needed:** Implement straight-line or declining-balance depreciation calculation.

---

### STUB-007 — Quotation → Sale Conversion Not Implemented

**File:** `app/Http/Controllers/Company/QuotationController.php`  
**Status:** Quotations can be created and status can be changed to "accepted", but there is no "Convert to Sale" button or logic. An accepted quotation cannot be turned into an actual sale order.  
**Work Needed:** Add a `convertToSale()` method that creates a `Sale` record from a `Quotation`.

---

### STUB-008 — Sales Return Does Not Restore Stock

**File:** `app/Http/Controllers/Company/SalesReturnController.php` (not audited in detail)  
**Status:** Based on the migration, `sales_returns` table exists but there is no evidence of stock restoration logic when a return is approved. Returned items should increment stock back.  
**Work Needed:** On sales return creation/approval, increment `stocks.quantity` and create a `stock_movement` of type `return_in`.

---

### STUB-009 — Purchase Return Does Not Reduce Stock

**Status:** Same issue as STUB-008 but for purchase returns. When items are returned to a supplier, stock should be decremented.  
**Work Needed:** On purchase return creation, decrement `stocks.quantity` and create a `stock_movement` of type `return_out`.

---

### STUB-010 — No Email Verification Enforcement for Company Admin

**File:** `routes/web.php` — Line 209  
**Status:** Company Admin routes use `verified` middleware, but there is no email verification flow triggered when a Super Admin creates a company (the company admin user is created programmatically). The `email_verified_at` may be null, locking the admin out.  
**Work Needed:** Either auto-verify email on programmatic user creation, or remove `verified` middleware from company routes.

---

## 6. SECURITY VULNERABILITIES 🔒

### SEC-001 — `CURLOPT_SSL_VERIFYPEER = false` in Production

_(See BUG-010 above — CRITICAL)_

---

### SEC-002 — Debug Logging of Sensitive Payment Data

**File:** `app/Http/Controllers/Company/SubscriptionController.php` — Lines 222-231  
**Problem:** `Log::info('SSLCommerz Request Debug', [...])` logs `store_id` and partial `store_pass` to the application log. In production, log files may be accessible or leaked. Even partial credentials are a security risk.  
**Fix:** Remove or gate these debug logs behind `APP_DEBUG === true`.

---

### SEC-003 — `SUPER_ADMIN_EMAIL` Read from `env()` Directly in Controller

**File:** `app/Http/Controllers/Company/SubscriptionController.php` — Line 556  
**Problem:** `$superAdminEmail = env('SUPER_ADMIN_EMAIL')` — calling `env()` directly in a controller is an anti-pattern in Laravel. After `php artisan config:cache` is run, `env()` returns `null` for all variables. This will silently break admin notification emails in production.  
**Fix:** Add `SUPER_ADMIN_EMAIL` to `config/app.php` and use `config('app.super_admin_email')`.

---

### SEC-004 — No Rate Limiting on POS Checkout Endpoint

**File:** `routes/web.php` — Line 385  
**Problem:** `POST /branch/pos/checkout` has no rate limiting. A malicious user could flood this endpoint to exhaust stock or create thousands of sale records.  
**Fix:** Add `throttle:60,1` middleware to the checkout route.

---

### SEC-005 — Backup Files Stored in `storage/app/backups/` Without Encryption

**File:** `app/Http/Controllers/SuperAdmin/SystemController.php`  
**Problem:** Database backups (full SQL dumps including all tenant data) are stored unencrypted in the application's storage directory. If the server is compromised, all tenant data is exposed.  
**Fix:** Encrypt backup files before storing, or upload to a secure off-site location (S3, etc.).

---

### SEC-006 — `reLoginCompanyAdmin()` Could Log In Wrong User

**File:** `app/Http/Controllers/Company/SubscriptionController.php` — Lines 660-688  
**Problem:** After payment callback, the system logs in the "first" Company Admin found for the company. If a company has multiple users with the Company Admin role, it may log in the wrong person.  
**Fix:** Store the `user_id` in the `Transaction` record when the payment is initiated, and use that specific user for re-login.

---

### SEC-007 — No CSRF Protection on Payment Callback Route

**File:** `routes/web.php` — Line 351  
**Problem:** `Route::match(['get', 'post'], '/company/subscription/payment/callback', ...)` — this route is outside the `auth` middleware group (intentionally, for SSLCommerz POST). However, it also bypasses CSRF verification. While this is necessary for payment gateways, there is no IPN signature verification to confirm the POST actually came from SSLCommerz.  
**Fix:** Implement SSLCommerz IPN hash verification using `val_id` and the SSLCommerz validation API.

---

## 7. PERFORMANCE ISSUES 🚀

### PERF-001 — N+1 Query in Branch Inventory Report

**File:** `app/Http/Controllers/Branch/ReportController.php` — Lines 145-148  
**Problem:**

```php
$totalStockVal = Stock::with('variant')
    ->where('branch_id', $branchId)
    ->get()  // Loads ALL stock records into memory
    ->sum(fn($s) => $s->quantity * ($s->variant->cost_price ?? 0));
```

This loads ALL stock records into PHP memory to calculate a sum. For large inventories, this is very slow.  
**Fix:** Use a database-level join and sum:

```php
$totalStockVal = DB::table('stocks')
    ->join('product_variants', 'stocks.variant_id', '=', 'product_variants.id')
    ->where('stocks.branch_id', $branchId)
    ->sum(DB::raw('stocks.quantity * product_variants.cost_price'));
```

---

### PERF-002 — Company Dashboard Makes 6 Separate Queries

**File:** `app/Http/Controllers/Company/DashboardController.php`  
**Problem:** 6 individual `Sale::where(...)` queries are executed sequentially. These could be combined or cached.  
**Fix:** Use `DB::select()` with a single query, or cache dashboard stats for 5 minutes using `Cache::remember()`.

---

### PERF-003 — POS Product Grid Loads Up to 80 Variants Without Pagination

**File:** `app/Http/Controllers/Branch/PosController.php` — Line 514  
**Problem:** `->limit(80)` loads 80 product variants with eager-loaded relationships (product, category, tax, branchStock) in a single request. For companies with many products, this is slow.  
**Fix:** Implement cursor-based pagination or virtual scrolling on the frontend.

---

### PERF-004 — No Query Result Caching on Super Admin Dashboard

**File:** `app/Http/Controllers/SuperAdmin/DashboardController.php`  
**Problem:** The Super Admin dashboard runs 7+ aggregate queries on every page load, including `Transaction::sum()`, `Subscription::count()`, `Company::count()`, etc. These are expensive on large datasets.  
**Fix:** Cache dashboard stats with `Cache::remember('superadmin_dashboard', 300, fn() => [...])`.

---

### PERF-005 — Missing Database Indexes on Frequently Queried Columns

**Observed:** While `2026_07_28_000001_add_performance_indexes.php` exists, the following columns are frequently used in WHERE clauses but may lack indexes:

- `sales.branch_id` + `sales.created_at` (compound)
- `stock_movements.variant_id` + `stock_movements.branch_id`
- `held_orders.branch_id`
- `activity_logs.user_id` + `activity_logs.created_at`

**Fix:** Audit the performance indexes migration and add missing compound indexes.

---

## 8. CODE QUALITY ISSUES 🧹

### QUALITY-001 — Mixed Language in Code Comments

**Problem:** Many controller files contain Bengali comments mixed with English code (e.g., `// ১. সম্পূর্ণ ফর্ম ভ্যালিডেশন`, `// ক) মূল প্রোডাক্ট তৈরি`). While this is fine for a local team, it makes the codebase harder to maintain for international contributors and tools.  
**Recommendation:** Standardize all code comments to English.

---

### QUALITY-002 — `\Log::error()` Used Instead of `Log::error()` (Facade)

**File:** `app/Http/Controllers/Company/PurchaseController.php` — Line 128  
**Problem:** `\Log::error(...)` uses the global namespace. The file does not import the `Log` facade at the top.  
**Fix:** Add `use Illuminate\Support\Facades\Log;` and use `Log::error(...)`.

---

### QUALITY-003 — `ProductController::edit()` Filters by `status = 'active'` but `Category` Model Has No `status` Column

**File:** `app/Http/Controllers/Company/ProductController.php` — Lines 177-180  
**Problem:**

```php
$categories = Category::where('company_id', $companyId)->where('status', 'active')->get();
$brands     = Brand::where('company_id', $companyId)->where('status', 'active')->get();
$units      = Unit::where('company_id', $companyId)->where('status', 'active')->get();
$taxes      = Tax::where('company_id', $companyId)->where('status', 'active')->get();
```

The `categories`, `brands`, `units`, and `taxes` tables use `is_active` (boolean), not `status` (string). This query will silently return empty results or throw an error.  
**Fix:** Change to `->where('is_active', true)`.

---

### QUALITY-004 — `ProductController::store()` Does Not Set `branch_id` on Stock

**File:** `app/Http/Controllers/Company/ProductController.php` — Lines 113-123  
**Problem:** When creating initial stock during product creation, `branch_id` is not set on the `Stock` record. The `stocks` table has a `branch_id` column. Stock without a `branch_id` is "central warehouse" stock, but the POS terminal queries stock by `branch_id`. This means newly created products may not appear in the POS.  
**Fix:** Either require a `branch_id` during product creation, or create stock records for all branches of the company.

---

### QUALITY-005 — `Sale` Model Missing `branch` Relationship in `HasCompanyScope`

**File:** `app/Models/Sale.php`  
**Problem:** The `Sale` model uses `HasCompanyScope` which automatically adds `WHERE company_id = ?` to all queries. However, the `branch()` relationship on `Sale` does a `belongsTo(Branch::class)` which may be affected by the global scope if `Branch` also has a company scope. This can cause unexpected empty results.  
**Fix:** Review all models using `HasCompanyScope` and ensure relationships are not accidentally scoped.

---

### QUALITY-006 — `Company::subscription()` Uses `hasOne()->latest()` Which is Unreliable

**File:** `app/Models/Company.php` — Lines 79-81  
**Problem:**

```php
public function subscription()
{
    return $this->hasOne(Subscription::class)->latest();
}
```

`hasOne()->latest()` is not a standard Laravel pattern. `hasOne` returns the first matching record, and `latest()` adds an `ORDER BY created_at DESC` but does not guarantee the "latest" record is returned when used as a relationship (especially with eager loading).  
**Fix:** Use `hasOne(Subscription::class)->latestOfMany()` (Laravel 8.42+) or `hasMany()->latest()->first()` in the controller.

---

### QUALITY-007 — No Form Request Classes for Complex Validations

**Problem:** All validation is done inline in controllers using `$request->validate([...])`. For complex forms like Product creation (with nested variants), Purchase creation (with items array), and Quotation creation, dedicated `FormRequest` classes would improve readability and reusability.  
**Recommendation:** Create `FormRequest` classes in `app/Http/Requests/` for at least: `StoreProductRequest`, `StorePurchaseRequest`, `StoreQuotationRequest`.

---

## 9. MISSING FEATURES (PLANNED BUT NOT BUILT) 📌

### FEAT-001 — No Multi-Currency Support

**Status:** The `Company` model has a `currency` field, but all monetary values are hardcoded in BDT (Bangladeshi Taka). There is no currency conversion, no currency symbol display based on company settings.

---

### FEAT-002 — No Customer Loyalty / Points System

**Status:** The `Customer` model has no loyalty points, credit balance, or purchase history summary. Many POS systems require this.

---

### FEAT-003 — No Product Image Upload in Product Form

**Status:** The `Product` model has an `image` field and the POS displays product images, but the product create/edit form does not have an image upload input. Images can only be set via seeder or direct DB entry.

---

### FEAT-004 — No Stock Alert / Low Stock Notification System

**Status:** Low stock is displayed on dashboards, but there is no automated email/notification when stock falls below `reorder_level`. The `reorder_level` column exists on both `ProductVariant` and `Stock` tables.

---

### FEAT-005 — No Expense Categories

**Status:** The `Expense` model has a `category` column (string), but there is no `ExpenseCategory` model or management UI. Users type category names freely, leading to inconsistent data.

---

### FEAT-006 — No Purchase Order (PO) System

**Status:** Purchases are recorded after the fact. There is no Purchase Order workflow (create PO → send to supplier → receive goods → auto-update stock).

---

### FEAT-007 — No Customer Credit / Due Payment Tracking

**Status:** The `Sale` model has `received_amount` and `total_amount`, but there is no `due_amount` tracking or customer credit management. If a customer pays partially, the due amount is not tracked.

---

### FEAT-008 — No Barcode Generation for New Products

**Status:** The `BarcodeController` exists for printing existing barcodes, but there is no auto-generation of barcodes when a new product variant is created without a barcode.

---

### FEAT-009 — No Shift Report / Cash Reconciliation

**Status:** Shifts can be opened and closed, but there is no shift summary report showing: opening cash, total sales, total cash received, expected closing cash, actual closing cash, and variance.

---

### FEAT-010 — No API for Mobile App Integration

**Status:** There are no REST API endpoints (no `/api` routes). All routes return Blade views or JSON for internal AJAX. If a mobile app or third-party integration is planned, a proper API layer is needed.

---

### FEAT-011 — No Webhook / IPN Verification for SSLCommerz

**Status:** The payment callback accepts any POST request claiming to be from SSLCommerz. There is no hash/signature verification using the SSLCommerz IPN validation API.

---

### FEAT-012 — No Automated Subscription Expiry Handling

**Status:** There is no scheduled job (cron) to automatically mark subscriptions as `expired` when `ends_at` passes. The `CheckSubscription` middleware checks on each request, but the `status` column in the database is never automatically updated to `expired`.  
**Fix:** Create an Artisan command `subscriptions:expire` and schedule it daily in `routes/console.php`.

---

### FEAT-013 — No Two-Factor Authentication (2FA)

**Status:** No 2FA for Super Admin or Company Admin accounts. Given this is a financial SaaS system, 2FA is strongly recommended.

---

### FEAT-014 — No Audit Trail for Super Admin Actions

**Status:** The `ActivityLog` model exists and the `LogActivity` trait is used on some models, but Super Admin actions (creating companies, modifying plans, impersonating users) are not consistently logged.

---

## 10. TESTING COVERAGE 🧪

### Current Test Files:

```
tests/
├── Feature/
│   ├── ExampleTest.php          (1 basic test)
│   ├── ProfileTest.php          (profile update tests)
│   └── Auth/                    (auth tests from Breeze)
│   └── SuperAdmin/              (exists but not audited)
└── Unit/
    ├── BillingModelsTest.php    (billing model tests)
    ├── ExampleTest.php          (1 basic test)
    └── SubscriptionGatewayTest.php (subscription tests)
```

### Assessment:

- ❌ **No tests for POS checkout logic** (most critical business logic)
- ❌ **No tests for stock deduction** (race condition prevention)
- ❌ **No tests for subscription middleware** (redirect loop prevention)
- ❌ **No tests for tenant isolation** (company_id scoping)
- ❌ **No tests for purchase/sales return stock reversal**
- ❌ **No tests for payroll generation**
- ⚠️ **Test coverage is estimated at < 10%** of the codebase

### Recommended Test Priority:

1. `PosCheckoutTest` — test stock deduction, price validation, race conditions
2. `TenantIsolationTest` — ensure Company A cannot access Company B's data
3. `SubscriptionMiddlewareTest` — test redirect loop prevention
4. `StockMovementTest` — test purchase/sale/return stock changes
5. `PaymentCallbackTest` — test SSLCommerz callback handling

---

## 11. DEVOPS / DEPLOYMENT ISSUES 🐳

### DEVOPS-001 — Docker Setup Uses `CURLOPT_SSL_VERIFYPEER = false` in Production

_(See BUG-010 / SEC-001)_

---

### DEVOPS-002 — No Queue Worker Health Check in Docker

**File:** `docker-compose.yml` / `docker-compose.prod.yml`  
**Problem:** Email sending uses `Mail::queue()` which requires a queue worker. If the queue worker crashes, emails are silently lost. There is no health check or restart policy for the queue worker container.  
**Fix:** Add `restart: unless-stopped` and a health check to the queue worker service.

---

### DEVOPS-003 — `APP_DEBUG=true` in `.env.example`

**File:** `.env.example` — Line 4  
**Problem:** The example env file has `APP_DEBUG=true`. If a developer copies this to production without changing it, full stack traces are exposed to end users.  
**Fix:** Change to `APP_DEBUG=false` in `.env.example` and add a note.

---

### DEVOPS-004 — No `.env` Validation on Startup

**Problem:** There is no startup check to ensure required environment variables (`SSLCOMMERZ_STORE_ID`, `MAIL_*`, `DB_*`) are set. Missing variables cause silent failures.  
**Fix:** Add a `php artisan env:check` command or use Laravel's `config/` validation.

---

### DEVOPS-005 — Storage Symlink Fallback Route is a Security Risk

**File:** `routes/web.php` — Lines 444-457  
**Problem:** The route `GET /storage/{path}` serves any file from `storage/app/public/` directly via PHP. While it has a `file_exists` check, it uses `mime_content_type()` which can be spoofed. If a malicious file is uploaded to storage, this route serves it.  
**Fix:** Validate file extensions against an allowlist (jpg, png, pdf, etc.) before serving.

---

## 12. PRIORITY ACTION PLAN 📅

> **Status key:** ✅ Fixed · 🔲 Open · ⏭️ Deferred

### 🔴 CRITICAL — All Fixed ✅

| #   | Issue                                                           | File                           | Effort | Status   |
| --- | --------------------------------------------------------------- | ------------------------------ | ------ | -------- |
| 1   | SSL_VERIFYPEER disabled in production                           | SubscriptionController.php:254 | 5 min  | ✅ Fixed |
| 2   | Balance Sheet returns hardcoded zeros                           | ReportController.php:64-74     | 2 hrs  | ✅ Fixed |
| 3   | Purchase delete does not reverse stock                          | PurchaseController.php:151-168 | 1 hr   | ✅ Fixed |
| 4   | Duplicate profile routes                                        | routes/web.php:417-421         | 5 min  | ✅ Fixed |
| 5   | `status` vs `is_active` filter bug in ProductController::edit() | ProductController.php:177-180  | 10 min | ✅ Fixed |

### 🟠 HIGH — All Fixed ✅

| #   | Issue                                                             | File                           | Effort | Status   |
| --- | ----------------------------------------------------------------- | ------------------------------ | ------ | -------- |
| 6   | Quotation number race condition                                   | QuotationController.php:72-73  | 1 hr   | ✅ Fixed |
| 7   | Sales/Purchase Return `price` → `unit_price` column name bug      | SalesReturnController.php (×2) | 1 hr   | ✅ Fixed |
| 8   | Employee increment missing company scope                          | EmployeeController.php:141     | 10 min | ✅ Fixed |
| 9   | Loan payment missing company scope                                | LoanController.php:119         | 10 min | ✅ Fixed |
| 10  | Cash transfer missing account ownership check                     | CashBookController.php:87-93   | 15 min | ✅ Fixed |
| 11  | Profit & Loss COGS calculation is wrong                           | ReportController.php:97-98     | 3 hrs  | ✅ Fixed |
| 12  | Debug payment logs expose credentials in production               | SubscriptionController.php:222 | 10 min | ✅ Fixed |
| 13  | `env()` called directly in controller (breaks after config:cache) | SubscriptionController.php:556 | 10 min | ✅ Fixed |

### 🟡 MEDIUM — Next Sprint (Open)

| #   | Issue                                                                   | Effort | Status  |
| --- | ----------------------------------------------------------------------- | ------ | ------- |
| 14  | `stock_movements.branch_id` NOT NULL breaks central-warehouse purchases | 30 min | 🔲 Open |
| 15  | Fix `Company::subscription()` `hasOne()->latest()` → `latestOfMany()`   | 15 min | 🔲 Open |
| 16  | Add subscription auto-expiry cron job (`subscriptions:expire`)          | 1 hr   | 🔲 Open |
| 17  | Add rate limiting to POS checkout endpoint (`throttle:60,1`)            | 15 min | 🔲 Open |
| 18  | Implement SSLCommerz IPN hash verification                              | 2 hrs  | 🔲 Open |
| 19  | Implement Quotation → Sale conversion                                   | 4 hrs  | 🔲 Open |
| 20  | Add product image upload to product create/edit form                    | 2 hrs  | 🔲 Open |
| 21  | Add payroll bonus/deduction edit UI                                     | 3 hrs  | 🔲 Open |
| 22  | Add stock alert / low-stock email notification                          | 4 hrs  | 🔲 Open |
| 23  | Write POS checkout tests (`PosCheckoutTest`)                            | 4 hrs  | 🔲 Open |
| 24  | Write tenant isolation tests (`TenantIsolationTest`)                    | 3 hrs  | 🔲 Open |

### 🟢 LOW — Backlog / Future

| #   | Feature                                         | Effort  | Status  |
| --- | ----------------------------------------------- | ------- | ------- |
| 25  | Connect MarketPro React frontend to backend API | 2 weeks | 🔲 Open |
| 26  | Implement Addon Marketplace lifecycle           | 1 week  | 🔲 Open |
| 27  | Add Two-Factor Authentication (2FA)             | 3 days  | 🔲 Open |
| 28  | Add Customer Loyalty/Points system              | 1 week  | 🔲 Open |
| 29  | Add Purchase Order (PO) workflow                | 1 week  | 🔲 Open |
| 30  | Add Customer Credit/Due tracking                | 3 days  | 🔲 Open |
| 31  | Add multi-currency support                      | 1 week  | 🔲 Open |
| 32  | Build REST API for mobile app                   | 2 weeks | 🔲 Open |
| 33  | Implement asset depreciation                    | 2 days  | 🔲 Open |
| 34  | Add shift cash reconciliation report            | 1 day   | 🔲 Open |
| 35  | Standardize all code comments to English        | 2 days  | 🔲 Open |

---

## SUMMARY SCORECARD

> Scores updated after August 2026 fix sprint.

| Category               | Before Fixes | After Fixes | Notes                                             |
| ---------------------- | ------------ | ----------- | ------------------------------------------------- |
| Core POS Functionality | 8/10         | **8/10**    | Solid, well-secured checkout                      |
| SaaS Billing           | 7/10         | **8/10**    | SSL fixed, env() fixed; IPN still missing         |
| ERP Modules            | 5/10         | **7/10**    | Returns fixed, stock reversal working             |
| Security               | 6/10         | **8/10**    | SSL + credential logging + ownership checks fixed |
| Performance            | 6/10         | **6/10**    | No performance changes yet                        |
| Code Quality           | 6/10         | **7/10**    | is_active bug fixed, lockForUpdate added          |
| Test Coverage          | 2/10         | **2/10**    | No new tests written yet                          |
| Documentation          | 7/10         | **9/10**    | CLAUDE.md + audit report updated                  |
| **Overall**            | **6/10**     | **7/10**    | **Significantly hardened — medium issues remain** |

---

_Report generated by AI Code Analyst. All line numbers reference the codebase as of August 2026. Please verify line numbers before applying fixes as the codebase may have changed._  
_Fix summary added August 2026 after engineer sprint._
