# Super Admin Panel — Full Audit Report

> **Audit Date:** 2026-07-30
> **Scope:** `resources/views/super-admin/`, `app/Http/Controllers/SuperAdmin/`, `resources/views/partials/sidebars/super_admin_sidebar.blade.php`, `routes/web.php` (Super Admin section, lines 103–176)
> **Mode:** Read-only diagnostic. No code was changed.

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [File Inventory](#2-file-inventory)
3. [Sidebar → Route → Controller Map](#3-sidebar--route--controller-map)
4. [Template Bloat & Junk Code](#4-template-bloat--junk-code)
5. [Functional & Live Features](#5-functional--live-features)
6. [Partially Built / Skeleton Items](#6-partially-built--skeleton-items)
7. [Completely Missing Features](#7-completely-missing-features)
8. [Controller Quality Notes](#8-controller-quality-notes)
9. [Prioritised Cleanup Plan](#9-prioritised-cleanup-plan)
10. [Risk Register](#10-risk-register)

---

## 1. Executive Summary

The Super Admin panel is **mostly wired and functional** for the core SaaS administration flows. All primary sidebar sections (Companies, Plans, Subscriptions, Users, Roles, Settings, System, Master Data, POS Customisation, Add-ons, Helpdesk, Reports) have corresponding controllers and routes registered in `web.php`.

However, the panel carries **significant template bloat** inherited from the purchased admin theme ("Zircos"). The dashboard page alone contains five hardcoded demo sections that are completely disconnected from real application data. Three sidebar entries are dead placeholders with `href="#"` and `Soon` badges. The company detail page (`show.blade.php`) has three stat cards that are intentionally gated behind `@isset` because the controller never passes the required variables.

**Overall health: 🟡 Functional core, dirty surface.**

---

## 2. File Inventory

### 2a. Views (`resources/views/super-admin/`)

| Path                                | Lines | Status                                                               |
| ----------------------------------- | ----- | -------------------------------------------------------------------- |
| `dashboard.blade.php`               | 877   | ⚠️ Bloated — real data mixed with demo widgets                       |
| `companies/index.blade.php`         | —     | ✅ Functional                                                        |
| `companies/create.blade.php`        | —     | ✅ Functional (heavy Branding/Theme section)                         |
| `companies/edit.blade.php`          | —     | ✅ Functional (heavy Branding/Theme section)                         |
| `companies/show.blade.php`          | 322   | ⚠️ 3 stat cards gated by `@isset` — controller never passes `$stats` |
| `plans/index.blade.php`             | —     | ✅ Functional                                                        |
| `plans/form.blade.php`              | —     | ✅ Functional (shared create/edit)                                   |
| `plans/show.blade.php`              | —     | ✅ Functional                                                        |
| `subscriptions/index.blade.php`     | —     | ✅ Functional                                                        |
| `subscriptions/create.blade.php`    | —     | ✅ Functional                                                        |
| `subscriptions/show.blade.php`      | —     | ✅ Functional                                                        |
| `transactions/index.blade.php`      | —     | ✅ Functional                                                        |
| `users/index.blade.php`             | —     | ✅ Functional                                                        |
| `users/create.blade.php`            | —     | ✅ Functional                                                        |
| `users/edit.blade.php`              | —     | ✅ Functional                                                        |
| `users/show.blade.php`              | —     | ✅ Functional                                                        |
| `roles/index.blade.php`             | —     | ✅ Functional                                                        |
| `roles/create.blade.php`            | —     | ✅ Functional                                                        |
| `roles/edit.blade.php`              | —     | ✅ Functional                                                        |
| `settings/general.blade.php`        | —     | ✅ Functional                                                        |
| `settings/payment.blade.php`        | —     | ✅ Functional                                                        |
| `settings/email.blade.php`          | —     | ✅ Functional                                                        |
| `system/logs.blade.php`             | —     | ✅ Functional                                                        |
| `system/backup.blade.php`           | —     | ✅ Functional                                                        |
| `system/info.blade.php`             | —     | ✅ Functional                                                        |
| `business-types/index.blade.php`    | —     | ✅ Functional                                                        |
| `business-types/create.blade.php`   | —     | ✅ Functional                                                        |
| `business-modules/index.blade.php`  | —     | ✅ Functional                                                        |
| `global-categories/index.blade.php` | —     | ✅ Functional                                                        |
| `global-units/index.blade.php`      | —     | ✅ Functional                                                        |
| `global-taxes/index.blade.php`      | —     | ✅ Functional                                                        |
| `global-attributes/index.blade.php` | —     | ✅ Functional                                                        |
| `invoice-templates/index.blade.php` | —     | ✅ Functional                                                        |
| `barcode-settings/index.blade.php`  | —     | ✅ Functional                                                        |
| `email-templates/index.blade.php`   | —     | ✅ Functional                                                        |
| `addons/index.blade.php`            | —     | ✅ Functional                                                        |
| `support-tickets/index.blade.php`   | —     | ✅ Functional                                                        |
| `tenants/index.blade.php`           | —     | ✅ Functional                                                        |
| `announcements/index.blade.php`     | —     | ✅ Functional                                                        |
| `reports/index.blade.php`           | 98    | ✅ Functional (revenue + date filter + paginated table)              |

> **Note:** `subscriptions/partials/` directory exists but contains no files — empty directory, safe to ignore or remove.

### 2b. Controllers (`app/Http/Controllers/SuperAdmin/`)

| Controller                   | Methods Present                                                                      | Status                                  |
| ---------------------------- | ------------------------------------------------------------------------------------ | --------------------------------------- |
| `DashboardController`        | `index()`                                                                            | ✅ Real DB queries                      |
| `CompanyController`          | `index, create, store, show, edit, update, destroy, impersonate, leaveImpersonation` | ✅ Full CRUD + impersonation            |
| `PlanController`             | `index, create, store, show, edit, update, destroy`                                  | ✅ Full CRUD                            |
| `SubscriptionController`     | `index, create, store, show, cancel, suspend, reactivate, extend`                    | ✅ Full lifecycle                       |
| `TransactionController`      | `index`                                                                              | ✅ Read-only listing                    |
| `UserController`             | `index, create, store, edit, update, destroy`                                        | ✅ Full CRUD                            |
| `RoleController`             | `index, create, store, edit, update, destroy`                                        | ✅ Full CRUD                            |
| `SettingController`          | `index, general, payment, email, update`                                             | ✅ Unified update with group validation |
| `SystemController`           | `logs, backup, info`                                                                 | ✅ Real filesystem/DB reads             |
| `BusinessTypeController`     | `index, create, store, destroy`                                                      | ✅ Functional                           |
| `BusinessModuleController`   | `index, store, update, destroy`                                                      | ✅ Functional                           |
| `GlobalCategoryController`   | `index, store, update, destroy`                                                      | ✅ Functional                           |
| `GlobalUnitController`       | `index, store, update, destroy`                                                      | ✅ Functional                           |
| `GlobalTaxController`        | `index, store, update, destroy`                                                      | ✅ Functional                           |
| `GlobalAttributeController`  | `index, store, update, destroy`                                                      | ✅ Functional                           |
| `InvoiceTemplateController`  | `index, store, update, destroy`                                                      | ✅ Functional                           |
| `BarcodeSettingController`   | `index, store, update, destroy`                                                      | ✅ Functional                           |
| `EmailTemplateController`    | `index, store, update, destroy`                                                      | ✅ Functional                           |
| `AddonController`            | `index, store, update, destroy`                                                      | ✅ Functional                           |
| `AddonMarketplaceController` | `index`                                                                              | ✅ Functional                           |
| `SupportTicketController`    | `index, show, update, destroy`                                                       | ✅ Functional                           |
| `AnnouncementController`     | `index, store, update, destroy`                                                      | ✅ Functional                           |
| `ImpersonateController`      | `index`                                                                              | ✅ Functional                           |
| `ReportController`           | `index`                                                                              | ✅ Real DB queries with date filter     |

**Total controllers: 24. Zero empty/skeleton files confirmed.**

---

## 3. Sidebar → Route → Controller Map

### 3a. Fully Wired (Sidebar → Route → Controller all present)

| Sidebar Label           | Route Name                                   | Controller@Method                  | Notes                                       |
| ----------------------- | -------------------------------------------- | ---------------------------------- | ------------------------------------------- |
| Dashboard               | `superadmin.dashboard`                       | `DashboardController@index`        | ✅                                          |
| All Companies           | `superadmin.companies.index`                 | `CompanyController@index`          | ✅                                          |
| Add Company             | `superadmin.companies.create`                | `CompanyController@create`         | ✅                                          |
| Pricing Plans           | `superadmin.plans.index`                     | `PlanController@index`             | ✅                                          |
| Active Subscriptions    | `superadmin.subscriptions.index`             | `SubscriptionController@index`     | ✅                                          |
| Payment Transactions    | `superadmin.transactions.index`              | `TransactionController@index`      | ✅                                          |
| Admin Staff             | `superadmin.users.index`                     | `UserController@index`             | ✅                                          |
| Roles & Permissions     | `superadmin.roles.index`                     | `RoleController@index`             | ✅                                          |
| General Setup           | `superadmin.settings.general`                | `SettingController@general`        | ✅                                          |
| Payment Gateways        | `superadmin.settings.payment`                | `SettingController@payment`        | ✅                                          |
| Email & SMS             | `superadmin.settings.email`                  | `SettingController@email`          | ✅                                          |
| Activity Logs           | `superadmin.system.logs`                     | `SystemController@logs`            | ✅                                          |
| Database Backup         | `superadmin.system.backup`                   | `SystemController@backup`          | ✅                                          |
| System Info             | `superadmin.system.info`                     | `SystemController@info`            | ✅                                          |
| Industry Types          | `superadmin.business-types.index`            | `BusinessTypeController@index`     | ✅                                          |
| Module Mapping          | `superadmin.business-modules.index`          | `BusinessModuleController@index`   | ✅                                          |
| Categories              | `superadmin.global-categories.index`         | `GlobalCategoryController@index`   | ✅                                          |
| Units (UOM)             | `superadmin.global-units.index`              | `GlobalUnitController@index`       | ✅                                          |
| Taxes & VAT             | `superadmin.global-taxes.index`              | `GlobalTaxController@index`        | ✅                                          |
| Attributes (Color/Size) | `superadmin.global-attributes.index`         | `GlobalAttributeController@index`  | ✅                                          |
| Invoice Templates       | `superadmin.invoice-templates.index`         | `InvoiceTemplateController@index`  | ✅                                          |
| Barcode Settings        | `superadmin.barcode-settings.index`          | `BarcodeSettingController@index`   | ✅                                          |
| Email Templates         | `superadmin.email-templates.index`           | `EmailTemplateController@index`    | ✅                                          |
| Installed Add-ons       | `superadmin.addons.index`                    | `AddonController@index`            | ✅                                          |
| Marketplace             | `superadmin.addons.marketplace`              | `AddonMarketplaceController@index` | ✅                                          |
| Support Tickets         | `superadmin.support-tickets.index`           | `SupportTicketController@index`    | ✅                                          |
| Login As Tenant         | `superadmin.tenants.index`                   | `ImpersonateController@index`      | ✅                                          |
| Announcements           | `superadmin.announcements.index`             | `AnnouncementController@index`     | ✅                                          |
| SaaS Revenue            | `superadmin.reports.index?type=revenue`      | `ReportController@index`           | ✅ (query param)                            |
| Tenant Usage            | `superadmin.reports.index?type=tenant-usage` | `ReportController@index`           | ⚠️ Same view, `type` param not used in view |
| My Profile              | `profile.edit`                               | `ProfileController@edit`           | ✅ (shared auth route)                      |

### 3b. Dead / Placeholder Sidebar Entries

| Sidebar Label      | Current `href` | Route Exists?                  | Controller Exists?                 |
| ------------------ | -------------- | ------------------------------ | ---------------------------------- |
| Clear Cache        | `#`            | ❌ No route                    | ❌ No action in `SystemController` |
| Update Application | `#`            | ❌ No route                    | ❌ No controller                   |
| Helpdesk / Support | `#`            | ❌ No route                    | ❌ No `HelpdeskController`         |
| Documentation      | `#`            | ❌ External URL not configured | N/A                                |

All four are annotated with `{{-- TODO: --}}` comments in the sidebar blade file. Three show a `Soon` badge in the UI.

---

## 4. Template Bloat & Junk Code

### 4a. `dashboard.blade.php` — **CRITICAL** (Lines 1–877)

This is the single largest source of template contamination in the Super Admin area. It mixes real data with hardcoded demo content from the "Zircos" admin theme.

#### Real data widgets (lines 24–88) — ✅ Keep

These four cards are backed by `DashboardController@index` and render live DB values:

- Monthly Revenue (`$billingStats['monthly_revenue']`)
- Total Revenue (`$billingStats['total_revenue']`)
- Active Subscriptions (`$billingStats['active_subscriptions']`)
- Available Plans (`$billingStats['total_plans']`)

#### Real charts (lines 163–184) — ✅ Keep

- `revenueChart` — Chart.js line chart fed by `$revenueChart` (last 6 months of real transactions)
- `planChart` — Chart.js doughnut chart fed by `$planDistribution` (real plan subscription counts)

#### Real expiring subscriptions table (lines 186–227) — ✅ Keep

- Backed by `$expiringSoon` from the controller. Fully functional.

#### Hardcoded demo widgets (lines 90–159) — ❌ **DELETE**

| Widget          | Line Range | Problem                                                     |
| --------------- | ---------- | ----------------------------------------------------------- |
| Total Visitors  | 91–106     | `data-counter data-target="701.8"` — hardcoded fake number  |
| Unique Visitors | 108–123    | `data-counter data-target="467.25"` — hardcoded fake number |
| Page Views      | 125–141    | `data-counter data-target="2.5"` — hardcoded fake number    |
| Bounce Rate     | 143–159    | `data-counter data-target="42.7"` — hardcoded fake number   |

These four cards have no controller backing. They use `data-counter` JavaScript to animate up to hardcoded target values. They are pure theme demo content and have no meaning in a SaaS admin panel.

#### Hardcoded demo chart sections (lines 229–330) — ❌ **DELETE**

| Section               | ID                  | Problem                                                                                       |
| --------------------- | ------------------- | --------------------------------------------------------------------------------------------- |
| Daily Sales           | `data-visits-chart` | ApexCharts placeholder, no data binding, hardcoded date range "March 26 2025 - April 01 2026" |
| Statistics            | `statistics-chart`  | ApexCharts placeholder, no data binding, same hardcoded date range                            |
| Total Revenue (chart) | `daily-sales`       | ApexCharts placeholder, no data binding, same hardcoded date range                            |

All three sections contain identical dropdown menus with `javascript:void(0)` items: "Sales Report", "Export Report", "Profit", "Action" — these are theme demo dropdowns with no functionality.

#### Hardcoded demo tables (lines 333–835) — ❌ **DELETE**

| Section                                   | Problem                                                                                                                                                                                                    |
| ----------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| "Brands Listing" table (lines 333–553)    | Fully hardcoded rows: ElectroMart USA, FurniStyle UK, AutoGear Germany, StyleCore Italy, TechVerse India. Static pagination. No Blade loop. No controller data.                                            |
| "Recent New Signup" table (lines 556–835) | Fully hardcoded rows: John Doe, Jane Smith, Michael Brown, Emily Davis, Robert Taylor. Uses `avatar-1.jpg` through `avatar-5.jpg` from theme assets. Static pagination. No Blade loop. No controller data. |

#### Breadcrumb brand residue (line 14) — ❌ **FIX**

```blade
<li class="breadcrumb-item"><a href="javascript: void(0);">Zircos</a></li>
```

`Zircos` is the purchased admin theme's brand name. Should be replaced with the application name (e.g., `config('app.name')` or the `site_name` setting from the `Setting` model).

**Summary of dashboard bloat:**

- ~650 of 877 lines are demo/template content
- Only ~227 lines contain real application data
- The page renders 4 fake metric cards, 3 fake ApexCharts, 2 fully fake data tables, and a wrong brand name in the breadcrumb

---

### 4b. `companies/show.blade.php` — **MEDIUM** (Lines 251–316)

The "Platform Usage" section contains three stat cards (`Products`, `Categories`, `Total Sales`) that are intentionally gated behind `@isset($stats['products'])`, `@isset($stats['categories'])`, and `@isset($stats['sales'])`.

The inline comment at line 251 explains the history:

> _"Products/Categories/Total Sales were hardcoded dummy numbers (1,240 / 89 / 5,432) in the old version with no real data behind them."_

**Current state:** The cards are hidden because `CompanyController@show` never passes a `$stats` array. The view is correctly guarded, but the dead card HTML still exists in the template.

**Action required:** Either:

1. Add `withCount` queries in `CompanyController@show` and pass `$stats`, **or**
2. Remove the three `@isset` blocks entirely until the feature is built

---

### 4c. `companies/create.blade.php` & `companies/edit.blade.php` — **LOW**

Both files contain large "Branding & Theme" sections with live preview blocks and inline-style theme rendering. These appear to be legitimate features (custom primary colour, logo upload, subdomain configuration for tenants). However, they are visually heavy and may contain theme-derived UI patterns.

**Recommendation:** Validate that all form fields in the Branding section map to actual `Company` model columns before the next sprint. No immediate action required.

---

### 4d. `super_admin_sidebar.blade.php` — **LOW** (Lines 205–453)

Four sidebar entries use `href="#"` with `{{-- TODO: --}}` comments:

```blade
<!-- Line 207 -->
<a href="#" {{-- TODO: replace with route('superadmin.system.cache-clear') when ready --}} ...>
    Clear Cache
    <span class="badge bg-warning-lt text-warning ms-auto">Soon</span>
</a>

<!-- Line 428 -->
<a href="#" {{-- TODO: replace with route('superadmin.system.update') when ready --}} ...>
    Update Application
    <span class="badge bg-warning-lt text-warning ms-auto">Soon</span>
</a>

<!-- Line 437 -->
<a href="#" {{-- TODO: replace with your actual docs URL --}} ...>
    Documentation
    <span class="badge bg-info-lt text-info ms-auto">↗</span>
</a>

<!-- Line 448 -->
<a href="#" {{-- TODO: replace with route('superadmin.helpdesk.index') when ready --}} ...>
    Helpdesk / Support
    <span class="badge bg-warning-lt text-warning ms-auto">Soon</span>
</a>
```

These are correctly annotated and visually marked. They are not broken — they just do nothing. No immediate risk.

---

## 5. Functional & Live Features

All of the following are confirmed wired end-to-end (route → middleware → controller → view):

### SaaS Management

- ✅ **Companies / Tenants** — Full CRUD + impersonation (login as tenant) + leave impersonation
- ✅ **Pricing Plans** — Full CRUD with feature flags and limits
- ✅ **Active Subscriptions** — Full lifecycle: create, view, cancel, suspend, reactivate, extend
- ✅ **Payment Transactions** — Read-only listing with pagination

### Platform Administration

- ✅ **Admin Staff (Users)** — Full CRUD for Super Admin panel users
- ✅ **Roles & Permissions** — Full CRUD with permission assignment

### Global Settings

- ✅ **General Setup** — Site name, timezone, locale, etc. via `Setting` model
- ✅ **Payment Gateways** — SSLCommerz sandbox/live, currency, symbol position
- ✅ **Email & SMS** — SMTP/mail driver configuration
- ✅ **Unified `update()` method** — Single POST handler for all three setting groups with group validation, password/secret field protection, and JSON field handling

### System & Security

- ✅ **Activity Logs** — Reads last 500 lines of `storage/logs/laravel.log`
- ✅ **Database Backup** — Lists files from `storage/app/backups/` with size and date
- ✅ **System Info** — PHP version, Laravel version, server software, DB driver, disk usage

### Global Master Data

- ✅ **Business Types** — index, create, store, destroy
- ✅ **Business Modules** — index, store, update, destroy
- ✅ **Global Categories** — index, store, update, destroy
- ✅ **Global Units (UOM)** — index, store, update, destroy
- ✅ **Global Taxes & VAT** — index, store, update, destroy
- ✅ **Global Attributes** — index, store, update, destroy

### POS & Customisation

- ✅ **Invoice Templates** — index, store, update, destroy
- ✅ **Barcode Settings** — index, store, update, destroy
- ✅ **Email Templates** — index, store, update, destroy
- ✅ **Installed Add-ons** — index, store, update, destroy
- ✅ **Marketplace** — index (read-only listing)

### Helpdesk & Support

- ✅ **Support Tickets** — index, show, update, destroy
- ✅ **Login As Tenant (Impersonate)** — index listing of all companies
- ✅ **Announcements** — index, store, update, destroy

### Global Reports

- ✅ **Revenue Report** — Date-filtered, paginated transaction table with summary cards (Revenue, New Subscriptions, Cancelled, New Companies)

---

## 6. Partially Built / Skeleton Items

### 6a. Reports — Single view, two sidebar links

The sidebar has two separate entries pointing to the same route with different `?type=` query parameters:

```blade
route('superadmin.reports.index', ['type' => 'revenue'])
route('superadmin.reports.index', ['type' => 'tenant-usage'])
```

However, `reports/index.blade.php` (98 lines) only renders a revenue/transaction report. The `?type=tenant-usage` parameter is passed in the URL but **never read or acted upon in the view or controller**. The "Tenant Usage" sidebar link navigates to the same revenue report page.

**Status:** The `revenue` report is complete. The `tenant-usage` report is a named placeholder — the sidebar entry exists, the route resolves, but no tenant usage data is fetched or displayed.

### 6b. `companies/show.blade.php` — Missing `$stats` from controller

As documented in §4b, three stat cards (Products, Categories, Total Sales) are present in the view but permanently hidden because `CompanyController@show` does not pass `$stats`. The view is correctly guarded with `@isset` but the feature is incomplete.

### 6c. `subscriptions/partials/` — Empty directory

The directory `resources/views/super-admin/subscriptions/partials/` exists but contains no files. This is a leftover from a planned partial extraction that was never completed. Safe to delete.

---

## 7. Completely Missing Features

### 7a. Clear Cache

- **Sidebar:** Present with `Soon` badge
- **Route:** ❌ Not registered in `web.php`
- **Controller:** ❌ No `cacheClear()` method in `SystemController`
- **Implementation note:** Should be a `POST` route (not `GET`) for CSRF safety. Would call `Artisan::call('cache:clear')`, `Artisan::call('config:clear')`, `Artisan::call('view:clear')`, etc.

### 7b. Update Application

- **Sidebar:** Present with `Soon` badge
- **Route:** ❌ Not registered
- **Controller:** ❌ No controller or action exists
- **Implementation note:** Complex feature — typically involves pulling from a remote update server, running migrations, and clearing caches. High risk; should be carefully scoped before implementation.

### 7c. Helpdesk / Support (Internal)

- **Sidebar:** Present with `Soon` badge (separate from the "Support Tickets" feature which IS built)
- **Route:** ❌ No `superadmin.helpdesk.*` routes
- **Controller:** ❌ No `HelpdeskController`
- **Clarification:** "Support Tickets" (`superadmin.support-tickets.*`) IS built and functional. This "Helpdesk / Support" entry in the "Resources & Support" section appears to be a separate, unbuilt internal helpdesk or knowledge base feature.

### 7d. Documentation

- **Sidebar:** Present with external link icon
- **Route:** N/A (external URL)
- **Status:** `href="#"` — no actual documentation URL has been configured. The `{{-- TODO: --}}` comment says to replace with `https://docs.yourapp.com`.

### 7e. Tenant Usage Report

- **Sidebar:** Present as "Tenant Usage" link
- **Route:** Resolves to `superadmin.reports.index?type=tenant-usage`
- **View:** ❌ The `type` parameter is never read — the same revenue report renders regardless
- **Controller:** ❌ `ReportController@index` does not branch on `?type`

---

## 8. Controller Quality Notes

### DashboardController (52 lines)

- Clean, focused, no bloat
- All five `$billingStats` values use real Eloquent queries
- `$revenueChart` uses `selectRaw` with `YEAR/MONTH` grouping — **MySQL-specific**, will break on SQLite/PostgreSQL
- `$planDistribution` uses `withCount` with a closure — correct pattern
- `$expiringSoon` uses `whereBetween` with `now()` and `now()->addDays(7)` — correct

### SystemController (68 lines)

- `logs()`: Reads last 500 lines of `laravel.log` — safe for small logs, but `File::lines()->toArray()` loads the entire file into memory first. For large production logs this could cause memory exhaustion. Should use `tail` or a chunked read.
- `backup()`: Lists files from `storage/app/backups/` — no actual backup creation logic. The page is a backup _viewer_, not a backup _creator_. No "Create Backup" button/route exists.
- `info()`: Uses `disk_total_space()` and `disk_free_space()` — correct. `$_SERVER['SERVER_SOFTWARE']` may be empty in CLI/queue contexts but is fine for web requests.

### SettingController (113 lines)

- Unified `update()` method handles all three setting groups via a hidden `group` input
- Password/secret field protection is implemented (skips empty values)
- `'on'` → `'1'` checkbox normalisation is present
- JSON field handling for `supported_currencies` is present
- **Potential issue:** All three POST routes (`settings.general.update`, `settings.payment.update`, `settings.email.update`) map to the same `SettingController@update` method. This is intentional and works, but means a malicious user could POST `group=payment` to the general settings URL. The `in:general,payment,email` validation prevents this.

### ReportController

- Uses date filter (`$from`, `$to`) with `whereBetween`
- Returns paginated `$transactions` with `withQueryString()` for pagination links
- Does not branch on `?type` parameter — `tenant-usage` report is unimplemented

---

## 9. Prioritised Cleanup Plan

### Priority 1 — Dashboard Bloat (High Impact, Low Risk)

**File:** `resources/views/super-admin/dashboard.blade.php`

Delete the following blocks:

1. Lines 90–159: Four fake metric cards (Total Visitors, Unique Visitors, Page Views, Bounce Rate)
2. Lines 229–330: Three fake ApexCharts sections (Daily Sales, Statistics, Total Revenue chart)
3. Lines 333–553: Hardcoded "Brands Listing" table
4. Lines 556–835: Hardcoded "Recent New Signup" table
5. Line 14: Replace `Zircos` with `{{ config('app.name') }}`

**Replace with:** Real SaaS-relevant widgets such as:

- Total registered companies (active / trial / suspended breakdown)
- Recent company signups (last 5, from DB)
- Recent transactions (last 5, from DB)
- Top plans by subscription count

**Estimated effort:** 2–3 hours

---

### Priority 2 — Company Show Stats (Medium Impact, Low Risk)

**File:** `resources/views/super-admin/companies/show.blade.php`
**File:** `app/Http/Controllers/SuperAdmin/CompanyController.php`

In `CompanyController@show`, add:

```php
$stats = [
    'products'   => $company->products()->count(),
    'categories' => $company->categories()->count(),
    'sales'      => $company->sales()->count(),
];
```

Pass `$stats` to the view. The three `@isset` blocks will then render automatically.

**Estimated effort:** 30 minutes (assuming `Company` model has `products()`, `categories()`, `sales()` relationships)

---

### Priority 3 — Tenant Usage Report (Medium Impact, Medium Effort)

**File:** `resources/views/super-admin/reports/index.blade.php`
**File:** `app/Http/Controllers/SuperAdmin/ReportController.php`

Add branching on `?type` in the controller:

```php
$type = $request->input('type', 'revenue');
if ($type === 'tenant-usage') {
    // fetch per-company usage stats
    return view('super-admin.reports.tenant-usage', ...);
}
```

Create `resources/views/super-admin/reports/tenant-usage.blade.php` with a table showing per-company: users count, branches count, products count, sales count, last active date.

**Estimated effort:** 3–4 hours

---

### Priority 4 — Clear Cache Action (Low Impact, Low Risk)

**File:** `routes/web.php` (Super Admin section)
**File:** `app/Http/Controllers/SuperAdmin/SystemController.php`
**File:** `resources/views/partials/sidebars/super_admin_sidebar.blade.php`

Add to `SystemController`:

```php
public function cacheClear()
{
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return back()->with('success', 'All caches cleared successfully.');
}
```

Add to `web.php` (inside the `system` prefix group):

```php
Route::post('/cache-clear', [SystemController::class, 'cacheClear'])->name('cache-clear');
```

Update sidebar to use a small form with CSRF instead of `<a href>`.

Remove `Soon` badge from sidebar entry.

**Estimated effort:** 1 hour

---

### Priority 5 — Sidebar Cleanup (Low Impact, Low Risk)

**File:** `resources/views/partials/sidebars/super_admin_sidebar.blade.php`

- Remove the `Documentation` entry entirely until a real URL is available, **or** set `href="{{ config('app.docs_url', '#') }}"` and add `docs_url` to `.env`
- Keep `Update Application` and `Helpdesk / Support` with `Soon` badges until those features are scoped

---

### Priority 6 — SystemController Log Memory Issue (Low Impact, Medium Risk)

**File:** `app/Http/Controllers/SuperAdmin/SystemController.php`

Replace:

```php
$lines = File::lines($logPath)->toArray();
$logContent = implode("\n", array_slice($lines, -500));
```

With a memory-safe tail read:

```php
$logContent = shell_exec("tail -500 " . escapeshellarg($logPath)) ?? '';
```

Or use a chunked file read from the end. The current implementation loads the entire log file into a PHP array before slicing — on a busy production server with a multi-GB log file this will cause a fatal memory error.

**Estimated effort:** 30 minutes

---

## 10. Risk Register

| #   | Risk                                                                                  | Severity  | Likelihood                   | File(s)                                               | Mitigation                                             |
| --- | ------------------------------------------------------------------------------------- | --------- | ---------------------------- | ----------------------------------------------------- | ------------------------------------------------------ |
| R1  | Dashboard fake data misleads Super Admin into thinking the platform has 701k visitors | 🔴 High   | Certain (it's already there) | `dashboard.blade.php` L90–159                         | Delete fake widgets immediately (Priority 1)           |
| R2  | `SystemController::logs()` loads entire log file into memory                          | 🟠 Medium | High on busy server          | `SystemController.php` L18–19                         | Replace with `tail` or chunked read (Priority 6)       |
| R3  | `?type=tenant-usage` silently renders wrong report                                    | 🟡 Low    | Certain                      | `reports/index.blade.php`                             | Add type branching (Priority 3)                        |
| R4  | `CompanyController@show` never passes `$stats` — 3 cards permanently hidden           | 🟡 Low    | Certain                      | `companies/show.blade.php`                            | Add withCount queries (Priority 2)                     |
| R5  | `revenueChart` query uses MySQL-specific `YEAR()`/`MONTH()` functions                 | 🟡 Low    | Low (MySQL is the target DB) | `DashboardController.php` L29–38                      | Use Carbon date formatting if DB portability is needed |
| R6  | `Documentation` sidebar link goes nowhere                                             | 🟢 Info   | Certain                      | `super_admin_sidebar.blade.php` L437                  | Set real docs URL or remove entry                      |
| R7  | `subscriptions/partials/` empty directory                                             | 🟢 Info   | N/A                          | `resources/views/super-admin/subscriptions/partials/` | Delete directory                                       |

---

## Appendix: Route Registration Summary (web.php lines 103–176)

```
Route::middleware(['auth', 'verified', 'role:Super Admin'])
    ->prefix('super-admin')
    ->name('superadmin.')

  GET  /dashboard                          superadmin.dashboard
  GET  /companies                          superadmin.companies.index
  GET  /companies/create                   superadmin.companies.create
  POST /companies                          superadmin.companies.store
  GET  /companies/{company}                superadmin.companies.show
  GET  /companies/{company}/edit           superadmin.companies.edit
  PUT  /companies/{company}                superadmin.companies.update
  DEL  /companies/{company}                superadmin.companies.destroy
  POST /companies/{company}/impersonate    superadmin.companies.impersonate
  GET  /plans (+ full resource)            superadmin.plans.*
  GET  /transactions                       superadmin.transactions.index
  GET  /subscriptions                      superadmin.subscriptions.index
  GET  /subscriptions/create               superadmin.subscriptions.create  ← declared BEFORE resource
  POST /subscriptions                      superadmin.subscriptions.store
  GET  /subscriptions/{sub}                superadmin.subscriptions.show
  POST /subscriptions/{sub}/cancel         superadmin.subscriptions.cancel
  POST /subscriptions/{sub}/suspend        superadmin.subscriptions.suspend
  POST /subscriptions/{sub}/reactivate     superadmin.subscriptions.reactivate
  POST /subscriptions/{sub}/extend         superadmin.subscriptions.extend
  GET  /users (+ CRUD except show)         superadmin.users.*
  GET  /roles (+ CRUD except show)         superadmin.roles.*
  GET  /settings/general                   superadmin.settings.general
  POST /settings/general                   superadmin.settings.general.update
  GET  /settings/payment                   superadmin.settings.payment
  POST /settings/payment                   superadmin.settings.payment.update
  GET  /settings/email                     superadmin.settings.email
  POST /settings/email                     superadmin.settings.email.update
  GET  /system/logs                        superadmin.system.logs
  GET  /system/backup                      superadmin.system.backup
  GET  /system/info                        superadmin.system.info
  GET  /business-types (+ partial CRUD)    superadmin.business-types.*
  GET  /business-modules (+ CRUD)          superadmin.business-modules.*
  GET  /global-categories (+ CRUD)         superadmin.global-categories.*
  GET  /global-units (+ CRUD)              superadmin.global-units.*
  GET  /global-taxes (+ CRUD)              superadmin.global-taxes.*
  GET  /global-attributes (+ CRUD)         superadmin.global-attributes.*
  GET  /invoice-templates (+ CRUD)         superadmin.invoice-templates.*
  GET  /barcode-settings (+ CRUD)          superadmin.barcode-settings.*
  GET  /email-templates (+ CRUD)           superadmin.email-templates.*
  GET  /addons/marketplace                 superadmin.addons.marketplace  ← declared BEFORE resource
  GET  /addons (+ CRUD)                    superadmin.addons.*
  GET  /support-tickets (+ partial CRUD)   superadmin.support-tickets.*
  GET  /announcements (+ CRUD)             superadmin.announcements.*
  GET  /tenants                            superadmin.tenants.index
  GET  /reports                            superadmin.reports.index
```

**Total registered Super Admin routes: ~55**
**Routes with no sidebar entry:** `companies.show`, `companies.edit`, `companies.update`, `companies.destroy`, `plans.show`, `plans.edit`, `plans.update`, `plans.destroy`, `subscriptions.cancel`, `subscriptions.suspend`, `subscriptions.reactivate`, `subscriptions.extend` — all are action/detail routes correctly not shown in the sidebar.

---

_End of Super Admin Panel Audit Report_
