# CLAUDE.md — Cloud POS Inventory v5

This file provides essential context for AI assistants (Claude, Cline, Copilot, etc.) working on this project.
Read this file **before** making any changes to the codebase.

---

## 🏗️ PROJECT OVERVIEW

**Name:** Cloud POS Inventory v5  
**Type:** Multi-tenant SaaS POS + ERP System  
**Stack:** Laravel 12 · PHP 8.2+ · MySQL · Blade Templates · Tailwind CSS · Alpine.js · Inertia.js/React (partial)  
**Payment Gateway:** SSLCommerz (Bangladesh)  
**Roles:** Super Admin → Company Admin → Manager / Salesman

---

## 🗂️ DIRECTORY STRUCTURE

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── SuperAdmin/     ← SaaS owner panel (22 controllers)
│   │   ├── Company/        ← Shop owner panel (22 controllers)
│   │   ├── Branch/         ← POS terminal panel (12 controllers)
│   │   └── Tenant/         ← Isolated attribute controller (1 file)
│   ├── Middleware/
│   │   ├── CheckSubscription.php          ← Blocks expired tenants
│   │   ├── CheckSubscriptionActive.php
│   │   ├── EnsureTenantAccess.php
│   │   ├── HandleInertiaRequests.php
│   │   └── IdentifyTenantByDomain.php
│   └── Requests/           ← Form Request classes (sparse — most validation is inline)
├── Models/                 ← 55+ Eloquent models
├── Services/
│   ├── TenantProvisioningService.php  ← Auto-setup on company creation
│   ├── TenantService.php
│   └── EmailTemplateService.php
├── Traits/
│   ├── LogActivity.php        ← Auto audit logging on model events
│   └── (Models/Traits/HasCompanyScope.php)  ← Auto company_id scoping
└── Policies/

resources/
├── views/                  ← PRIMARY UI (Blade)
│   ├── super-admin/
│   ├── company/
│   ├── branch/
│   ├── layouts/
│   ├── partials/
│   ├── pdf/
│   └── payment/
└── js/Pages/               ← Inertia/React (only Welcome, Auth, MarketPro)

database/
├── migrations/             ← 50+ migration files
└── seeders/                ← 15+ seeders

routes/
├── web.php                 ← ALL routes (Super Admin + Company + Branch)
├── auth.php                ← Laravel Breeze auth routes
├── frontend.php            ← MarketPro frontend routes
└── console.php             ← Scheduled commands
```

---

## 🔐 MULTI-TENANCY ARCHITECTURE

**Pattern:** Single-database multi-tenancy using `company_id` column on every tenant table.

### How it works:

1. Every tenant model uses the `HasCompanyScope` trait → automatically adds `WHERE company_id = ?` to all queries.
2. The `EnsureTenantAccess` middleware verifies the logged-in user belongs to the correct company.
3. The `CheckSubscription` middleware blocks Company Admin access if subscription is expired/suspended.

### User → Company → Branch hierarchy:

```
users.company_id  →  companies.id   (Company Admin belongs to a company)
users.branch_id   →  branches.id    (Manager/Salesman belongs to a branch)
branches.company_id → companies.id  (Branch belongs to a company)
```

### Role names (Spatie Laravel Permission):

- `Super Admin` — SaaS owner, no company_id
- `Company Admin` — Shop owner, has company_id, no branch_id
- `Manager` — Branch manager, has company_id AND branch_id
- `Salesman` — Branch cashier, has company_id AND branch_id

---

## 🛣️ ROUTE STRUCTURE

| Prefix         | Middleware                                                              | Panel                        |
| -------------- | ----------------------------------------------------------------------- | ---------------------------- |
| `/super-admin` | `auth, verified, role:Super Admin`                                      | Super Admin                  |
| `/company`     | `auth, verified, role:Company Admin, tenant.access, subscription.check` | Company Admin                |
| `/branch`      | `auth, verified, role:Manager\|Salesman, tenant.access`                 | Branch                       |
| `/`            | public                                                                  | Welcome page (Inertia/React) |

### Named route prefixes:

- `superadmin.*` — Super Admin routes
- `company.*` — Company Admin routes
- `branch.*` — Branch routes

---

## 💳 PAYMENT GATEWAY (SSLCommerz)

**Flow:**

1. Company Admin clicks "Subscribe" → `POST /company/subscription/subscribe/{plan}`
2. Server creates a `pending` Transaction record
3. Server POSTs to SSLCommerz API → gets `GatewayPageURL`
4. User is redirected to SSLCommerz hosted payment page
5. SSLCommerz POSTs back to `/company/subscription/payment/callback` (PUBLIC route, no auth)
6. Callback handler verifies transaction, activates subscription, re-logs in the Company Admin
7. User is redirected to `/company/dashboard` with success message

**Important:** The callback route is PUBLIC (outside auth middleware) because SSLCommerz's cross-origin POST drops the session cookie. The `reLoginCompanyAdmin()` method restores the session after payment.

**Environment variables:**

```
SSLCOMMERZ_STORE_ID=
SSLCOMMERZ_STORE_PASSWORD=
SSLCOMMERZ_IS_SANDBOX=true   # false for production
```

**✅ Fixed:** `CURLOPT_SSL_VERIFYPEER` is now `!$isSandbox` — disabled only in sandbox mode, enabled in production.

---

## 📦 KEY MODELS & RELATIONSHIPS

```
Company
  ├── hasMany Users
  ├── hasMany Branches
  ├── hasMany Products
  ├── hasMany Sales
  ├── hasOne Subscription (latest)   ← uses hasOne()->latest() — unreliable, use latestOfMany()
  ├── belongsTo Plan
  └── belongsToMany BusinessModules (pivot: company_module)

Product
  ├── belongsTo Company
  ├── belongsTo Category
  ├── belongsTo Brand
  └── hasMany ProductVariants
        ├── belongsTo Tax
        ├── belongsTo Unit
        └── hasMany Stocks (one per branch)

Sale
  ├── belongsTo Company
  ├── belongsTo Branch
  ├── belongsTo Customer
  ├── belongsTo User
  └── hasMany SaleItems
        └── belongsTo ProductVariant

Stock
  ├── belongsTo Company
  ├── belongsTo Branch      ← NULL = Central Warehouse
  └── belongsTo ProductVariant
```

---

## 🏪 POS TERMINAL (Branch)

**File:** `app/Http/Controllers/Branch/PosController.php`

### Security features:

- Server-side price validation — client-supplied prices are **ignored**. Prices are always fetched from DB.
- `lockForUpdate()` on Stock rows to prevent race conditions.
- Shift enforcement — user must have an open shift before checkout.
- Customer company validation — customer must belong to the same company.

### Checkout flow:

1. `GET /branch/pos` — loads POS page with categories, customers, products, held orders
2. `GET /branch/pos/products` — AJAX product grid with category/search filter
3. `GET /branch/pos/search` — barcode/SKU/name search
4. `POST /branch/pos/checkout` — atomic checkout (DB transaction + stock deduction + movement log)
5. `GET /branch/pos/invoice/{sale}/print` — thermal receipt print view

### Hold orders:

- `POST /branch/pos/hold` — saves cart to `held_orders` table
- `GET /branch/pos/held-orders` — lists held orders for branch
- `DELETE /branch/pos/held-orders/{id}` — discards held order

---

## 🏢 TENANT PROVISIONING

**File:** `app/Services/TenantProvisioningService.php`

When a new company is created, `provision()` automatically:

1. Creates default Units (from GlobalUnit master data or hardcoded fallback)
2. Creates default Taxes (from GlobalTax master data or hardcoded fallback)
3. Creates default Categories (from GlobalCategory master data)
4. Creates a "Walk-in Customer" record
5. Creates default Attributes (from GlobalAttribute or business-type-specific defaults)
6. Assigns default BusinessModules (core modules always enabled)
7. Sends welcome email using the `welcome-tenant` EmailTemplate

---

## 📊 ACTIVITY LOGGING

**Trait:** `app/Traits/LogActivity.php`

Drop `use LogActivity;` on any model to get automatic audit logging.

```php
// Opt-in only specific fields:
protected array $logOnly = ['status', 'total_amount'];

// Opt-out specific fields:
protected array $logExclude = ['image', 'updated_at'];
```

Logs are stored in `activity_logs` table and viewable at `/super-admin/system/logs`.

---

## 🔧 KNOWN BUGS — STATUS

> Full details in `docs/ENGINEER_AUDIT_REPORT.md`
> **Last updated:** August 2026 — All Critical + High items fixed ✅

| #   | Bug                                                               | File                         | Priority    | Status   |
| --- | ----------------------------------------------------------------- | ---------------------------- | ----------- | -------- |
| 1   | `CURLOPT_SSL_VERIFYPEER = false` in production                    | SubscriptionController.php   | 🔴 CRITICAL | ✅ Fixed |
| 2   | Balance Sheet returns hardcoded zeros                             | ReportController.php         | 🔴 CRITICAL | ✅ Fixed |
| 3   | Purchase delete does NOT reverse stock                            | PurchaseController.php       | 🔴 CRITICAL | ✅ Fixed |
| 4   | Duplicate `/profile` routes in web.php                            | routes/web.php               | 🔴 CRITICAL | ✅ Fixed |
| 5   | `status` vs `is_active` bug in ProductController::edit()          | ProductController.php        | 🔴 CRITICAL | ✅ Fixed |
| 6   | Quotation number race condition (count() not atomic)              | QuotationController.php      | 🟠 HIGH     | ✅ Fixed |
| 7   | Sales Return `price` column → should be `unit_price`              | SalesReturnController.php    | 🟠 HIGH     | ✅ Fixed |
| 8   | Purchase Return `price` column → should be `unit_price`           | PurchaseReturnController.php | 🟠 HIGH     | ✅ Fixed |
| 9   | Employee increment missing company scope check                    | EmployeeController.php       | 🟠 HIGH     | ✅ Fixed |
| 10  | Loan payment missing company scope check                          | LoanController.php           | 🟠 HIGH     | ✅ Fixed |
| 11  | Cash transfer missing account ownership check                     | CashBookController.php       | 🟠 HIGH     | ✅ Fixed |
| 12  | Profit & Loss COGS calculation is wrong                           | ReportController.php         | 🟠 HIGH     | ✅ Fixed |
| 13  | `env()` called directly in controller (breaks after config:cache) | SubscriptionController.php   | 🟠 HIGH     | ✅ Fixed |
| 14  | Debug payment logs expose credentials in production               | SubscriptionController.php   | 🟠 HIGH     | ✅ Fixed |

### ⚠️ Known Pre-existing Schema Issue (Not Fixed — Out of Scope)

- **Central-warehouse purchases** (`branch_id = NULL`) will hit a `NOT NULL` constraint on `stock_movements.branch_id`.
  This is a pre-existing schema design issue. Needs a migration to make `stock_movements.branch_id` nullable.

---

## ⚠️ INCOMPLETE FEATURES (Stubs)

| Feature                              | Status                                              |
| ------------------------------------ | --------------------------------------------------- |
| Sales Return stock restoration       | ✅ Already implemented (stock restored on creation) |
| Purchase Return stock deduction      | ✅ Already implemented (stock reduced on creation)  |
| Quotation → Sale conversion          | ❌ Not implemented                                  |
| Payroll bonus/deduction edit UI      | ❌ Not implemented                                  |
| Product image upload in form         | ❌ Not implemented                                  |
| Asset depreciation calculation       | ❌ Not implemented                                  |
| Addon Marketplace (install/activate) | ❌ UI only                                          |
| MarketPro React frontend             | ❌ Disconnected from backend                        |
| Subscription auto-expiry cron job    | ❌ Not implemented                                  |
| SSLCommerz IPN hash verification     | ❌ Not implemented                                  |

---

## 🚀 DEVELOPMENT COMMANDS

```bash
# Start all services (server + queue + logs + vite)
composer dev

# Run migrations
php artisan migrate

# Seed the database
php artisan db:seed

# Seed only Super Admin
php artisan db:seed --class=SuperAdminSeeder

# Clear all caches
php artisan cache:clear && php artisan config:clear && php artisan view:clear && php artisan route:clear

# Run tests
composer test

# Build frontend assets
npm run build

# Dev frontend (hot reload)
npm run dev
```

---

## 🐳 DOCKER

```bash
# Start with Docker Compose
docker-compose up -d

# Production
docker-compose -f docker-compose.prod.yml up -d
```

---

## 🌐 ENVIRONMENT VARIABLES (Required)

```env
APP_NAME=CloudPOS
APP_ENV=production          # IMPORTANT: change from 'local'
APP_DEBUG=false             # IMPORTANT: must be false in production
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=cloudpos
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_app_password
MAIL_FROM_ADDRESS=noreply@yourpos.com
SUPER_ADMIN_EMAIL=admin@yourpos.com   # ⚠️ Move to config/app.php

SSLCOMMERZ_STORE_ID=your_store_id
SSLCOMMERZ_STORE_PASSWORD=your_store_pass
SSLCOMMERZ_IS_SANDBOX=false           # false = live mode

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

---

## 🧪 TESTING

**Test files location:** `tests/Feature/` and `tests/Unit/`

**Current coverage:** ~10% (very low)

**Priority tests to write:**

1. `PosCheckoutTest` — stock deduction, price validation, race conditions
2. `TenantIsolationTest` — Company A cannot access Company B's data
3. `SubscriptionMiddlewareTest` — redirect loop prevention
4. `StockMovementTest` — purchase/sale/return stock changes
5. `PaymentCallbackTest` — SSLCommerz callback handling

```bash
php artisan test
php artisan test --filter=PosCheckoutTest
```

---

## 📋 CODE CONVENTIONS

### Controllers:

- Always scope queries by `company_id` — never trust client-supplied IDs without DB verification
- Use `DB::beginTransaction()` / `DB::commit()` / `DB::rollBack()` for multi-table writes
- Use `lockForUpdate()` on Stock rows during checkout to prevent race conditions
- Return `redirect()->back()->with('error', ...)` on failure, `redirect()->route(...)->with('success', ...)` on success

### Models:

- Tenant models MUST use `HasCompanyScope` trait
- Use `$logOnly` or `$logExclude` with `LogActivity` trait to control audit log verbosity
- Use `$casts` for boolean columns (`is_active`, `has_variants`) and JSON columns (`settings`, `attributes`)

### Validation:

- Always use company-scoped exists rules: `exists:categories,id,company_id,{$companyId}`
- Never trust client-supplied prices in financial transactions — always re-fetch from DB

### Routes:

- Super Admin routes: `superadmin.*`
- Company routes: `company.*`
- Branch routes: `branch.*`

---

## 📁 FULL AUDIT REPORT

See `docs/ENGINEER_AUDIT_REPORT.md` for the complete audit including:

- All bugs with exact file + line numbers and fix instructions
- Security vulnerabilities
- Performance issues
- Missing features list
- Priority action plan (35 items)
- Overall project scorecard

---

## 🗓️ PROJECT STATUS (August 2026)

> ✅ = Production Ready &nbsp;|&nbsp; ⚠️ = Has open issues &nbsp;|&nbsp; ❌ = Not implemented

| Module               | Status                                                    |
| -------------------- | --------------------------------------------------------- |
| POS Terminal         | ✅ Production Ready                                       |
| Inventory Management | ✅ Production Ready                                       |
| Purchase Management  | ✅ Fixed — delete now reverses stock                      |
| Sales Management     | ✅ Production Ready                                       |
| Sales Returns        | ✅ Fixed — stock restored + `unit_price` column corrected |
| Purchase Returns     | ✅ Fixed — stock reduced + `unit_price` column corrected  |
| Quotations           | ⚠️ Missing: convert to sale feature                       |
| Cash Book            | ✅ Fixed — account ownership validation added             |
| Loan Management      | ✅ Fixed — company scope check added                      |
| Asset Management     | ⚠️ Missing: depreciation calculation                      |
| HR / Employees       | ✅ Fixed — company scope check added to increments        |
| Payroll              | ⚠️ Missing: bonus/deduction edit UI                       |
| SaaS Billing         | ✅ Fixed — SSL peer verify + debug logs + env() fixed     |
| Reports              | ✅ Fixed — Balance Sheet real data + P&L uses COGS        |
| Super Admin Panel    | ✅ Mostly complete                                        |
| Email Templates      | ✅ Production Ready                                       |
| Activity Logs        | ✅ Production Ready                                       |
| Addon Marketplace    | ❌ UI only — no real install/activate logic               |
| MarketPro Frontend   | ❌ Disconnected from backend                              |
| Test Coverage        | ❌ ~10% — needs significant improvement                   |

### 🔧 Remaining Open Issues (Medium Priority)

| #   | Issue                                                                              | Next Step                                     |
| --- | ---------------------------------------------------------------------------------- | --------------------------------------------- |
| 1   | `stock_movements.branch_id` NOT NULL constraint breaks central-warehouse purchases | Add migration: make `branch_id` nullable      |
| 2   | `Company::subscription()` uses unreliable `hasOne()->latest()`                     | Change to `hasOne()->latestOfMany()`          |
| 3   | Quotation → Sale conversion missing                                                | Add `convertToSale()` method                  |
| 4   | Payroll bonus/deduction edit UI missing                                            | Add edit form for payroll entries             |
| 5   | Product image upload missing from create/edit form                                 | Add file upload input                         |
| 6   | No rate limiting on POS checkout endpoint                                          | Add `throttle:60,1` middleware                |
| 7   | SSLCommerz IPN hash verification missing                                           | Implement val_id verification                 |
| 8   | Subscription auto-expiry cron job missing                                          | Create `subscriptions:expire` Artisan command |
| 9   | Test coverage ~10%                                                                 | Write PosCheckoutTest, TenantIsolationTest    |
