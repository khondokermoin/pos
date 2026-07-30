# 🏢 Company Panel — Deep Diagnostic Audit Report

**Generated:** 2026-07-29  
**Auditor:** Principal Software Engineer (AI-assisted deep scan)  
**Scope:** All sidebar menu items → Controller methods → Blade views

---

## 📋 Task 1: Sidebar Route Map

| #   | Section       | Menu Label           | Route Name                          | HTTP |
| --- | ------------- | -------------------- | ----------------------------------- | ---- |
| 1   | Main          | Dashboard            | `company.dashboard`                 | GET  |
| 2   | Sales & POS   | All Sales            | `company.sales.index`               | GET  |
| 3   | Inventory     | All Products         | `company.products.index`            | GET  |
| 4   | Inventory     | Add Product          | `company.products.create`           | GET  |
| 5   | Inventory     | Categories           | `company.categories.index`          | GET  |
| 6   | Inventory     | Low Stock Alerts     | `company.inventory.low-stock`       | GET  |
| 7   | Inventory     | Stock Adjustment     | `company.inventory.stock-adjust`    | GET  |
| 8   | Inventory     | Stock Transfer       | `company.transfers.index`           | GET  |
| 9   | Purchasing    | All Purchases        | `company.purchases.index`           | GET  |
| 10  | Purchasing    | New Purchase         | `company.purchases.create`          | GET  |
| 11  | Purchasing    | Suppliers            | `company.suppliers.index`           | GET  |
| 12  | CRM & Finance | Customers            | `company.customers.index`           | GET  |
| 13  | CRM & Finance | Expenses             | `company.expenses.index`            | GET  |
| 14  | Branch & Ops  | Branches             | `company.branches.index`            | GET  |
| 15  | Branch & Ops  | Staff & Roles        | `company.users.index`               | GET  |
| 16  | Reports       | Sales Report         | `company.reports.sales`             | GET  |
| 17  | Reports       | Stock Report         | `company.reports.stock`             | GET  |
| 18  | Settings      | Company Profile      | `company.settings.profile`          | GET  |
| 19  | Settings      | Invoice Settings     | `company.settings.invoice`          | GET  |
| 20  | Settings      | Variant & Attributes | `company.settings.attributes.index` | GET  |
| 21  | Settings      | My Subscription      | `company.subscription.index`        | GET  |
| 22  | Settings      | Announcements        | `company.announcements.index`       | GET  |
| 23  | Account       | My Profile           | `profile.edit`                      | GET  |

> **Note:** Routes for sub-operations (store, update, destroy, show, edit) were also audited below even if not directly in the sidebar.

---

## 📋 Task 2: Deep Scan Results

### Legend

- ✅ **Implemented** — Real logic, real data, real UI
- ⚠️ **Stub** — Method body is `//` (empty comment only)
- 🔴 **0-byte** — File exists on disk but is completely empty (0 bytes)
- ❌ **Missing** — File does not exist at all
- 🟡 **Partial** — Logic exists but incomplete (e.g., no company_id scoping, wrong route redirect, broken wiring)

---

### 2.1 — Main

| Route               | Controller Method           | Status                                                      | View File                     | Status                                                                  |
| ------------------- | --------------------------- | ----------------------------------------------------------- | ----------------------------- | ----------------------------------------------------------------------- |
| `company.dashboard` | `DashboardController@index` | ✅ Full logic (KPI stats, recent sales, subscription alert) | `company/dashboard.blade.php` | ✅ Full UI (stat cards, recent sales table, subscription expiry banner) |

---

### 2.2 — Sales & POS

| Route                 | Controller Method      | Status                                                          | View File                       | Status                                                         |
| --------------------- | ---------------------- | --------------------------------------------------------------- | ------------------------------- | -------------------------------------------------------------- |
| `company.sales.index` | `SaleController@index` | ✅ Full logic (search, date filter, pagination, revenue totals) | `company/sales/index.blade.php` | ✅ Full UI (filter bar, data table, pagination, revenue cards) |

---

### 2.3 — Inventory Management

| Route                            | Controller Method                 | Status                                                                                | View File                                  | Status                                                         |
| -------------------------------- | --------------------------------- | ------------------------------------------------------------------------------------- | ------------------------------------------ | -------------------------------------------------------------- |
| `company.products.index`         | `ProductController@index`         | ✅ Full logic (paginated, with category/brand/variants/stock)                         | `company/products/index.blade.php`         | ✅ Full UI (avatar, SKU, price, stock badge, action buttons)   |
| `company.products.create`        | `ProductController@create`        | ✅ Full logic (loads categories, brands, units, taxes)                                | `company/products/create.blade.php`        | ✅ Full UI (dynamic variant builder, attribute rows, JS logic) |
| `company.products.store`         | `ProductController@store`         | ✅ Full logic (validation, DB transaction, stock + movement entry)                    | _(form action)_                            | ✅                                                             |
| `company.products.show`          | `ProductController@show`          | ✅ Logic exists (authorizeCompany, eager loads)                                       | `company/products/show.blade.php`          | ❌ **FILE MISSING**                                            |
| `company.products.edit`          | `ProductController@edit`          | ✅ Full logic (loads variants, decodes JSON attributes)                               | `company/products/edit.blade.php`          | ❌ **FILE MISSING**                                            |
| `company.products.update`        | `ProductController@update`        | ✅ Full logic (variant upsert, new stock entry)                                       | _(form action)_                            | ✅                                                             |
| `company.products.destroy`       | `ProductController@destroy`       | ✅ Full logic (history check, cascade delete)                                         | _(form action)_                            | ✅                                                             |
| `company.categories.index`       | `CategoryController@index`        | ✅ Full logic                                                                         | `company/categories/index.blade.php`       | ✅ Full UI (DataTables, search, CRUD actions)                  |
| `company.categories.create`      | `CategoryController@create`       | ✅                                                                                    | `company/categories/create.blade.php`      | ✅ Full UI                                                     |
| `company.categories.edit`        | `CategoryController@edit`         | ✅                                                                                    | `company/categories/edit.blade.php`        | ✅ Full UI                                                     |
| `company.inventory.low-stock`    | `InventoryController@lowStock`    | ⚠️ **STUB** — comment says "পরে এখানে লো-স্টক প্রোডাক্টের ডাটা আনবেন", passes no data | `company/inventory/low-stock.blade.php`    | 🔴 **0-BYTE**                                                  |
| `company.inventory.stock-adjust` | `InventoryController@stockAdjust` | ⚠️ **STUB** — returns view with zero data                                             | `company/inventory/stock_adjust.blade.php` | 🔴 **0-BYTE**                                                  |
| `company.transfers.index`        | `TransferController@index`        | ❌ **STUB** — class body is `//` only                                                 | `company/transfers/index.blade.php`        | 🔴 **0-BYTE**                                                  |
| `company.transfers.create`       | `TransferController@create`       | ❌ **STUB** — class body is `//` only                                                 | `company/transfers/create.blade.php`       | 🔴 **0-BYTE**                                                  |
| `company.transfers.store`        | `TransferController@store`        | ❌ **STUB** — class body is `//` only                                                 | _(form action)_                            | ❌                                                             |

---

### 2.4 — Purchasing

| Route                       | Controller Method            | Status                                                              | View File                            | Status                                                       |
| --------------------------- | ---------------------------- | ------------------------------------------------------------------- | ------------------------------------ | ------------------------------------------------------------ |
| `company.purchases.index`   | `PurchaseController@index`   | ✅ Full logic (paginated, with branch/supplier/user)                | `company/purchases/index.blade.php`  | 🔴 **0-BYTE**                                                |
| `company.purchases.create`  | `PurchaseController@create`  | ✅ Full logic (loads branches, suppliers, active variants)          | `company/purchases/create.blade.php` | ✅ Full UI (dynamic item rows, JS subtotal/grand total calc) |
| `company.purchases.store`   | `PurchaseController@store`   | ✅ Full logic (DB transaction, stock updateOrCreate, movement log)  | _(form action)_                      | ✅                                                           |
| `company.purchases.show`    | `PurchaseController@show`    | ✅ Logic exists (security check, eager loads items/variant/product) | `company/purchases/show.blade.php`   | 🔴 **0-BYTE**                                                |
| `company.purchases.destroy` | `PurchaseController@destroy` | ✅ Logic exists                                                     | _(form action)_                      | ✅                                                           |
| `company.suppliers.index`   | `SupplierController@index`   | ⚠️ **STUB** — `//` only                                             | `company/suppliers/index.blade.php`  | ❌ **FILE MISSING**                                          |
| `company.suppliers.store`   | `SupplierController@store`   | ⚠️ **STUB** — `//` only                                             | _(form action)_                      | ❌                                                           |
| `company.suppliers.update`  | `SupplierController@update`  | ⚠️ **STUB** — `//` only                                             | _(form action)_                      | ❌                                                           |
| `company.suppliers.destroy` | `SupplierController@destroy` | ⚠️ **STUB** — `//` only                                             | _(form action)_                      | ❌                                                           |

---

### 2.5 — CRM & Finance

| Route                       | Controller Method            | Status                  | View File                           | Status              |
| --------------------------- | ---------------------------- | ----------------------- | ----------------------------------- | ------------------- |
| `company.customers.index`   | `CustomerController@index`   | ⚠️ **STUB** — `//` only | `company/customers/index.blade.php` | ❌ **FILE MISSING** |
| `company.customers.store`   | `CustomerController@store`   | ⚠️ **STUB** — `//` only | _(form action)_                     | ❌                  |
| `company.customers.update`  | `CustomerController@update`  | ⚠️ **STUB** — `//` only | _(form action)_                     | ❌                  |
| `company.customers.destroy` | `CustomerController@destroy` | ⚠️ **STUB** — `//` only | _(form action)_                     | ❌                  |
| `company.expenses.index`    | `ExpenseController@index`    | ⚠️ **STUB** — `//` only | `company/expenses/index.blade.php`  | ❌ **FILE MISSING** |
| `company.expenses.store`    | `ExpenseController@store`    | ⚠️ **STUB** — `//` only | _(form action)_                     | ❌                  |
| `company.expenses.update`   | `ExpenseController@update`   | ⚠️ **STUB** — `//` only | _(form action)_                     | ❌                  |
| `company.expenses.destroy`  | `ExpenseController@destroy`  | ⚠️ **STUB** — `//` only | _(form action)_                     | ❌                  |

---

### 2.6 — Branch & Operations

| Route                       | Controller Method           | Status                                                      | View File                             | Status                                                                                              |
| --------------------------- | --------------------------- | ----------------------------------------------------------- | ------------------------------------- | --------------------------------------------------------------------------------------------------- |
| `company.branches.index`    | `BranchController@index`    | ✅ Full logic (paginated, with manager)                     | `company/branches/index.blade.php`    | 🟡 **Partial UI** — Edit button is `href="#"` (dead link), no delete button, no pagination rendered |
| `company.branches.create`   | `BranchController@create`   | ✅ Full logic                                               | `company/branches/create.blade.php`   | ✅ Full UI                                                                                          |
| `company.branches.store`    | `BranchController@store`    | ✅ Full logic (validation, Rule::exists scoped)             | _(form action)_                       | ✅                                                                                                  |
| `company.branches.show`     | `BranchController@show`     | ✅ Logic exists                                             | `company/branches/show.blade.php`     | ❌ **FILE MISSING**                                                                                 |
| `company.branches.edit`     | `BranchController@edit`     | ✅ Full logic                                               | `company/branches/edit.blade.php`     | ❌ **FILE MISSING**                                                                                 |
| `company.branches.update`   | `BranchController@update`   | ✅ Full logic                                               | _(form action)_                       | ✅                                                                                                  |
| `company.branches.destroy`  | `BranchController@destroy`  | ✅ Full logic                                               | _(form action)_                       | ✅                                                                                                  |
| `company.users.index`       | `UserController@index`      | ❌ **METHOD MISSING** — `index()` not defined in controller | `company/users/index.blade.php`       | 🔴 **0-BYTE**                                                                                       |
| `company.users.create`      | `UserController@create`     | ✅ Full logic                                               | `company/users/create.blade.php`      | ✅ Full UI                                                                                          |
| `company.users.store`       | `UserController@store`      | ✅ Full logic (validation, role assignment)                 | _(form action)_                       | ✅                                                                                                  |
| `company.users.assign-role` | `UserController@assignRole` | ❌ **METHOD MISSING** — `assignRole()` not defined          | `company/users/assign_role.blade.php` | 🔴 **0-BYTE**                                                                                       |

---

### 2.7 — Reports

| Route                   | Controller Method        | Status                                                            | View File                               | Status                                                        |
| ----------------------- | ------------------------ | ----------------------------------------------------------------- | --------------------------------------- | ------------------------------------------------------------- |
| `company.reports.sales` | `ReportController@sales` | ✅ Full logic (date range filter, totals, paginated sales)        | `company/reports/daily-sales.blade.php` | ✅ Full UI (filter form, 3 KPI cards, data table, pagination) |
| `company.reports.stock` | `ReportController@stock` | ✅ Full logic (stock with variant/product/branch, low/out counts) | `company/reports/stock.blade.php`       | ✅ Full UI (2 KPI cards, color-coded stock table)             |

---

### 2.8 — Settings & Account

| Route                               | Controller Method                  | Status                                                                                                       | View File                                     | Status                                                                        |
| ----------------------------------- | ---------------------------------- | ------------------------------------------------------------------------------------------------------------ | --------------------------------------------- | ----------------------------------------------------------------------------- |
| `company.settings.profile`          | `CompanySettingController@profile` | 🟡 **Partial** — returns view but passes **zero data** (no company model loaded)                             | `company/settings/profile.blade.php`          | 🔴 **0-BYTE**                                                                 |
| `company.settings.invoice`          | `CompanySettingController@invoice` | 🟡 **Partial** — returns view but passes **zero data**                                                       | `company/settings/invoice.blade.php`          | 🔴 **0-BYTE**                                                                 |
| `company.settings.attributes.index` | `Tenant\AttributeController@index` | 🟡 **Broken wiring** — redirects to `tenant.attributes.index` (wrong route name), not scoped to `company_id` | `company/settings/attributes/index.blade.php` | ❌ **FILE MISSING** (controller points to `tenant.settings.attributes.index`) |
| `company.subscription.index`        | `SubscriptionController@index`     | ✅ Full logic (subscription, transactions, plans)                                                            | `company/subscription/index.blade.php`        | ✅ Full UI (plan cards, payment history table, expiry badge)                  |
| `company.announcements.index`       | `AnnouncementController@index`     | 🟡 **Partial** — returns view but passes **zero data** (no announcements fetched from DB)                    | `company/announcements/index.blade.php`       | 🔴 **0-BYTE**                                                                 |
| `profile.edit`                      | `ProfileController@edit`           | ✅ (Breeze/Jetstream standard)                                                                               | _(Breeze default)_                            | ✅                                                                            |

---

## 📋 Task 3: Final Roadmap Report

---

### ✅ 100% Complete — Fully Implemented Controllers AND Views

These modules are production-ready with real business logic and premium UI:

| Module                                     | Routes Covered                                                |
| ------------------------------------------ | ------------------------------------------------------------- |
| **Dashboard**                              | `company.dashboard`                                           |
| **All Sales (Overview)**                   | `company.sales.index`                                         |
| **Products — List**                        | `company.products.index`                                      |
| **Products — Create/Store**                | `company.products.create`, `.store`                           |
| **Products — Update/Destroy**              | `company.products.update`, `.destroy`                         |
| **Categories — Full CRUD**                 | `company.categories.*` (index, create, edit, update, destroy) |
| **Purchases — Create/Store**               | `company.purchases.create`, `.store`                          |
| **Branches — Create/Store/Update/Destroy** | `company.branches.create`, `.store`, `.update`, `.destroy`    |
| **Users — Create/Store**                   | `company.users.create`, `.store`                              |
| **Sales Report**                           | `company.reports.sales`                                       |
| **Stock Report**                           | `company.reports.stock`                                       |
| **My Subscription**                        | `company.subscription.index`                                  |
| **My Profile**                             | `profile.edit`                                                |

---

### 🚧 Partially Built — Controller Exists But View is 0-Byte / Logic is Incomplete

These have a controller method but the view is an empty file, OR the controller passes no data:

| Module                       | Route                            | Problem                                                                                              |
| ---------------------------- | -------------------------------- | ---------------------------------------------------------------------------------------------------- |
| **Purchases — List**         | `company.purchases.index`        | ✅ Controller full, but `purchases/index.blade.php` is **0-byte**                                    |
| **Purchases — Show/Detail**  | `company.purchases.show`         | ✅ Controller full, but `purchases/show.blade.php` is **0-byte**                                     |
| **Branches — Index**         | `company.branches.index`         | ✅ Controller full, view exists but Edit button is `href="#"` (dead link), no delete button wired    |
| **Branches — Show**          | `company.branches.show`          | ✅ Controller full, but `branches/show.blade.php` is **FILE MISSING**                                |
| **Branches — Edit**          | `company.branches.edit`          | ✅ Controller full, but `branches/edit.blade.php` is **FILE MISSING**                                |
| **Products — Show**          | `company.products.show`          | ✅ Controller full, but `products/show.blade.php` is **FILE MISSING**                                |
| **Products — Edit**          | `company.products.edit`          | ✅ Controller full (full variant/attribute logic), but `products/edit.blade.php` is **FILE MISSING** |
| **Low Stock Alerts**         | `company.inventory.low-stock`    | ⚠️ Controller is a stub (no data passed), view is **0-byte**                                         |
| **Stock Adjustment**         | `company.inventory.stock-adjust` | ⚠️ Controller is a stub (no data passed), view is **0-byte**                                         |
| **Company Profile Settings** | `company.settings.profile`       | ⚠️ Controller returns view with zero data, view is **0-byte**                                        |
| **Invoice Settings**         | `company.settings.invoice`       | ⚠️ Controller returns view with zero data, view is **0-byte**                                        |
| **Announcements**            | `company.announcements.index`    | ⚠️ Controller returns view with zero data, view is **0-byte**                                        |
| **Users — Index**            | `company.users.index`            | ❌ `index()` method **not defined** in UserController, view is **0-byte**                            |
| **Users — Assign Role**      | `company.users.assign-role`      | ❌ `assignRole()` method **not defined** in UserController, view is **0-byte**                       |

---

### ❌ Missing Completely — No Controller Logic AND No View

These modules are pure empty stubs with zero implementation on both sides:

| Module                           | Routes Affected                                | Controller                                                                                                                       | View                                         |
| -------------------------------- | ---------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------- |
| **Suppliers**                    | `company.suppliers.index/store/update/destroy` | ⚠️ All methods are `//` stubs                                                                                                    | ❌ No view files exist at all                |
| **Customers**                    | `company.customers.index/store/update/destroy` | ⚠️ All methods are `//` stubs                                                                                                    | ❌ No view files exist at all                |
| **Expenses**                     | `company.expenses.index/store/update/destroy`  | ⚠️ All methods are `//` stubs                                                                                                    | ❌ No view files exist at all                |
| **Stock Transfers**              | `company.transfers.index/create/store`         | ❌ Controller class body is `//` (completely empty)                                                                              | 🔴 Both view files are 0-byte                |
| **Variant & Attribute Settings** | `company.settings.attributes.*`                | 🟡 Controller exists (`Tenant\AttributeController`) but redirects to wrong route (`tenant.attributes.index`), not company-scoped | ❌ No view at `company/settings/attributes/` |

---

### 🔧 Critical Bugs Found (Beyond Missing Views)

These are **runtime errors** that will crash the app even if you navigate to them:

| Bug                                                             | Location                           | Impact                                              |
| --------------------------------------------------------------- | ---------------------------------- | --------------------------------------------------- |
| `company.users.index` — `index()` method not defined            | `UserController.php`               | **500 Error** on navigation to Staff & Roles        |
| `company.users.assign-role` — `assignRole()` method not defined | `UserController.php`               | **500 Error** on PATCH request                      |
| `AttributeController` redirects to `tenant.attributes.index`    | `Tenant/AttributeController.php`   | **RouteNotFoundException** after save/update/delete |
| `branches/index.blade.php` Edit button is `href="#"`            | `branches/index.blade.php` line 53 | Edit branch is **non-functional** (dead link)       |
| `SettingController.php` is completely empty (`//`)              | `SettingController.php`            | Unused but registered — no impact yet               |

---

### 🎯 Recommended Next Sprint — Priority Build Order

Based on the audit, here is the recommended sprint sequence to unblock the most critical company workflows:

---

#### 🥇 Sprint 1 — Unblock Core Operations (Highest Business Impact)

**1. Suppliers Module** _(Prerequisite for Purchases)_

- Build `SupplierController` (index, store, update, destroy) with company_id scoping
- Build `company/suppliers/index.blade.php` with inline modal CRUD
- **Why first:** The Purchase form already works and loads suppliers — but if there are no suppliers, purchases cannot be created. This unblocks the entire purchasing workflow.

**2. Purchases — Index & Show Views** _(Controller is 100% ready)_

- Build `company/purchases/index.blade.php` (list with branch/supplier/date/amount)
- Build `company/purchases/show.blade.php` (purchase detail with line items)
- **Why:** Controller is fully implemented. These are 0-byte files — pure UI work, no backend needed.

---

#### 🥈 Sprint 2 — Complete Products & Inventory

**3. Products — Edit & Show Views** _(Controller is 100% ready)_

- Build `company/products/edit.blade.php` (reuse create.blade.php structure with pre-filled variant data)
- Build `company/products/show.blade.php` (product detail card with variant/stock table)
- **Why:** The edit controller has full variant/attribute JSON decode logic ready. Just needs the view.

**4. Branches — Edit View & Fix Index** _(Controller is 100% ready)_

- Build `company/branches/edit.blade.php`
- Fix `branches/index.blade.php` — wire the Edit button to `route('company.branches.edit', $branch)` and add Delete form
- **Why:** Branch management is critical for multi-branch operations.

**5. Low Stock Alerts & Stock Adjustment** _(Both controller + view need work)_

- Implement `InventoryController@lowStock` with real query (Stock ≤ reorder_level, with product/variant/branch)
- Implement `InventoryController@stockAdjust` with form + store action
- Build both views

---

#### 🥉 Sprint 3 — CRM & Finance

**6. Customers Module** _(Full CRUD — controller + view)_

- Implement `CustomerController` (index, store, update, destroy)
- Build `company/customers/index.blade.php` with inline modal or separate create page

**7. Expenses Module** _(Full CRUD — controller + view)_

- Implement `ExpenseController` (index, store, update, destroy)
- Build `company/expenses/index.blade.php`

---

#### 🏁 Sprint 4 — Settings, Staff & Transfers

**8. Users — Index & Assign Role** _(Critical bug fix)_

- Add `index()` method to `UserController` (list company users with roles/branch)
- Add `assignRole()` method
- Build `company/users/index.blade.php`
- Build `company/users/assign_role.blade.php`

**9. Stock Transfers** _(Full build — controller + views)_

- Implement `TransferController` (index, create, store) with branch-to-branch stock movement logic
- Build `company/transfers/index.blade.php` and `create.blade.php`

**10. Settings — Profile, Invoice, Attributes** _(Data + UI)_

- Implement `CompanySettingController@profile` to load and save company model
- Implement `CompanySettingController@invoice` to load/save invoice preferences
- Fix `AttributeController` to use `company.settings.attributes.index` route and scope by `company_id`
- Build all three views

**11. Announcements** _(Simple read-only)_

- Implement `AnnouncementController@index` to fetch announcements from DB
- Build `company/announcements/index.blade.php`

---

## 📊 Summary Scorecard

| Category                                            | Count  |
| --------------------------------------------------- | ------ |
| ✅ **100% Complete modules**                        | **13** |
| 🚧 **Partially built (view missing or 0-byte)**     | **14** |
| ❌ **Missing completely (no controller + no view)** | **5**  |
| 🔧 **Critical runtime bugs**                        | **5**  |
| **Total sidebar routes audited**                    | **23** |

**Overall Completion: ~35% of the Company Panel is production-ready.**

---

## 🗂️ Quick Reference: All Missing/Empty Files

```
# 0-BYTE VIEW FILES (exist on disk but empty):
resources/views/company/inventory/low-stock.blade.php
resources/views/company/inventory/stock_adjust.blade.php
resources/views/company/transfers/index.blade.php
resources/views/company/transfers/create.blade.php
resources/views/company/purchases/index.blade.php
resources/views/company/purchases/show.blade.php
resources/views/company/settings/profile.blade.php
resources/views/company/settings/invoice.blade.php
resources/views/company/announcements/index.blade.php
resources/views/company/users/index.blade.php
resources/views/company/users/assign_role.blade.php
resources/views/company/branches/form.blade.php        ← unused stub

# MISSING VIEW FILES (do not exist at all):
resources/views/company/products/show.blade.php
resources/views/company/products/edit.blade.php
resources/views/company/branches/show.blade.php
resources/views/company/branches/edit.blade.php
resources/views/company/suppliers/index.blade.php
resources/views/company/customers/index.blade.php
resources/views/company/expenses/index.blade.php
resources/views/company/settings/attributes/index.blade.php

# EMPTY/STUB CONTROLLERS:
app/Http/Controllers/Company/TransferController.php    ← class body is `//`
app/Http/Controllers/Company/SupplierController.php    ← all methods are `//`
app/Http/Controllers/Company/CustomerController.php    ← all methods are `//`
app/Http/Controllers/Company/ExpenseController.php     ← all methods are `//`
app/Http/Controllers/Company/InventoryController.php   ← methods return view with no data
app/Http/Controllers/Company/SettingController.php     ← class body is `//` (unused)
app/Http/Controllers/Company/UserController.php        ← missing index() and assignRole()
app/Http/Controllers/Company/CompanySettingController.php ← methods return view with no data
app/Http/Controllers/Company/AnnouncementController.php   ← returns view with no data
app/Http/Controllers/Tenant/AttributeController.php    ← wrong route names, not company-scoped
```
