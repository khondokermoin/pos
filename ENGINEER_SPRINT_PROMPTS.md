# 🛠️ Engineer Sprint Prompts — Company Panel Build-Out

**Based on:** `COMPANY_PANEL_AUDIT_REPORT.md`  
**Project:** Cloud POS Inventory v5 (Laravel 11, Blade, Bootstrap 5 / Tabler UI)  
**Architecture:** Multi-tenant SaaS — Company Admin panel under `/company` prefix, `company.` route namespace  
**Auth Guard:** `role:Company Admin` middleware, always scope queries by `Auth::user()->company_id`

> **How to use these prompts:** Copy each prompt block and paste it to your AI engineer (Cline/Cursor/ChatGPT) one sprint at a time. Each prompt is self-contained and references the exact files to create or modify.

---

## 🔧 HOTFIX PROMPT — Fix 5 Critical Runtime Bugs First

> **Run this BEFORE any sprint. These are live crashes.**

````
You are a Senior Laravel Engineer working on a multi-tenant SaaS POS system (Laravel 11, Blade, Tabler UI Bootstrap 5).

Fix the following 5 critical runtime bugs. Do NOT write new features — only fix these specific issues.

### Bug 1 & 2: UserController missing index() and assignRole() methods
File: `app/Http/Controllers/Company/UserController.php`

Add the following two methods to the existing UserController class:

1. `index()` method:
   - Get all users where `company_id = Auth::user()->company_id`
   - Eager load their roles (Spatie: `->with('roles')`) and branch (`->with('branch')`)
   - Paginate 15 per page
   - Return view `company.users.index` with compact('users')

2. `assignRole(Request $request, User $user)` method:
   - Security check: abort(403) if `$user->company_id !== Auth::user()->company_id`
   - Validate: `role` field required string
   - Call `$user->syncRoles([$request->role])`
   - Return redirect back with success message 'Role updated successfully.'

### Bug 3: AttributeController wrong route names
File: `app/Http/Controllers/Tenant/AttributeController.php`

The controller currently redirects to `tenant.attributes.index` which does not exist.
Change ALL three redirect calls:
- `route('tenant.attributes.index')` → `route('company.settings.attributes.index')`

Also add company_id scoping to the `index()` method:
- Change: `Attribute::query()->with('values')->latest()->get()`
- To: `Attribute::where('company_id', Auth::user()->company_id)->with('values')->latest()->get()`

Also fix the `store()` method:
- Change: `'company_id' => Auth::id() ? optional(Auth::user())->company_id : null`
- To: `'company_id' => Auth::user()->company_id`

### Bug 4: Branches index — dead Edit link and missing Delete button
File: `resources/views/company/branches/index.blade.php`

Find line 53: `<a href="#" class="btn btn-sm btn-info">Edit</a>`
Replace with:
```blade
<a href="{{ route('company.branches.edit', $branch->id) }}" class="btn btn-sm btn-warning btn-sm">
    <i class="ti ti-pencil"></i> Edit
</a>
<form action="{{ route('company.branches.destroy', $branch->id) }}" method="POST" class="d-inline"
      onsubmit="return confirm('Are you sure you want to delete this branch?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">
        <i class="ti ti-trash"></i> Delete
    </button>
</form>
````

Also add pagination below the table (before closing `</div>` of card-body):

```blade
<div class="mt-3">{{ $branches->links() }}</div>
```

### Bug 5: SettingController.php is empty

File: `app/Http/Controllers/Company/SettingController.php`
This file is registered but empty. It is safe to leave as-is for now (no routes point to it).
No action needed — just confirm it is not causing any route errors.

After all fixes, confirm:

- `company.users.index` route will now resolve without 500 error
- `company.users.assign-role` PATCH route will now resolve
- `company.settings.attributes.*` routes will redirect correctly
- Branch edit/delete buttons are functional

```

---

## 🥇 SPRINT 1 PROMPT — Suppliers Module + Purchases Views
> **Goal:** Unblock the entire Purchasing workflow.

```

You are a Senior Laravel Engineer working on a multi-tenant SaaS POS system.

Tech stack: Laravel 11, PHP 8.2, Blade templates, Tabler UI (Bootstrap 5), Spatie Permissions.
All company-scoped queries MUST use: `Auth::user()->company_id`
Layout to extend: `@extends('layouts.admin_master')`
Icon library: Tabler Icons (`ti ti-*`)

---

### TASK 1: Build SupplierController (Full CRUD)

File: `app/Http/Controllers/Company/SupplierController.php`

Replace the existing empty stub with a fully implemented controller.

The `suppliers` table has these columns:
`id, company_id, name, email, phone, address, contact_person, notes, status (active/inactive), created_at, updated_at`

Implement these methods (all scoped to `Auth::user()->company_id`):

**index():**

- Get suppliers paginated 15, latest first, where company_id matches
- Support search filter: `request('search')` on name/phone/email using `->when()`
- Return view `company.suppliers.index` with compact('suppliers')

**store(Request $request):**

- Validate: name (required, max 255), email (nullable, email), phone (nullable, max 20), address (nullable), contact_person (nullable), notes (nullable)
- Create supplier with company_id = Auth::user()->company_id, status = 'active'
- Return redirect()->route('company.suppliers.index')->with('success', 'Supplier added successfully.')

**update(Request $request, Supplier $supplier):**

- Security: abort(403) if supplier->company_id !== Auth::user()->company_id
- Same validation as store (email unique rule should ignore current record)
- Update and redirect back with success

**destroy(Supplier $supplier):**

- Security: abort(403) if supplier->company_id !== Auth::user()->company_id
- Check if supplier has purchases: if yes, return back with error 'Cannot delete supplier with purchase history.'
- Delete and redirect with success

---

### TASK 2: Build Suppliers View

File: `resources/views/company/suppliers/index.blade.php`

Build a premium Tabler UI page with:

**Page Header:**

- Title: "Suppliers" with breadcrumb (Dashboard > Suppliers)
- "Add Supplier" button that opens a Bootstrap modal (not a separate page)

**Stats Row (3 cards):**

- Total Suppliers (count)
- Active Suppliers
- Inactive Suppliers

**Search Bar:**

- GET form with `search` input, Filter button, Reset link

**Suppliers Table:**
Columns: #, Name, Contact Person, Phone, Email, Address, Status (badge), Actions (Edit modal trigger + Delete form)

**Add/Edit Modal:**

- Single Bootstrap modal with id="supplierModal"
- Fields: Name\*, Email, Phone, Address, Contact Person, Notes, Status (select: active/inactive)
- On Edit: populate modal fields via JavaScript using data-\* attributes on the Edit button
- Form action switches between store (POST) and update (PUT) based on mode
- Use `@csrf` and `@method('PUT')` hidden inputs

**Empty State:**

- Show a friendly empty state with icon when no suppliers exist

**JavaScript:**

- Function `openEditModal(id, name, email, phone, address, contact, notes, status)` that populates the modal and changes form action URL

---

### TASK 3: Build Purchases Index View

File: `resources/views/company/purchases/index.blade.php`

The controller already passes: `$purchases` (paginated collection with branch, supplier, user loaded)

Build a premium Tabler UI page with:

**Page Header:**

- Title: "All Purchases" with breadcrumb
- "New Purchase" button → `route('company.purchases.create')`

**Stats Row (3 cards):**

- Total Purchases (count from $purchases->total())
- Total Amount Spent (sum — pass from controller or compute in blade with @php)
- This Month's Purchases

**Filter Bar:**

- GET form: supplier dropdown, branch dropdown, date_from, date_to, search (invoice/ref)
- Filter + Reset buttons

**Purchases Table:**
Columns: #, Purchase Date, Supplier, Branch (or "Central Warehouse" if null), Items Count, Total Amount (৳), Status (badge), Created By, Actions (View button)

**Pagination:** `{{ $purchases->withQueryString()->links() }}`

**Empty State:** Friendly message with link to create new purchase

---

### TASK 4: Build Purchases Show View

File: `resources/views/company/purchases/show.blade.php`

The controller passes: `$purchase` (with items.variant.product, branch, supplier, user loaded)

Build a purchase detail page with:

**Page Header:**

- Title: "Purchase #{{ $purchase->id }}" with breadcrumb
- "Back to List" button

**Purchase Info Card (2 columns):**
Left: Supplier name, contact, Purchase Date, Status badge
Right: Branch name (or "Central Warehouse"), Created by (user name), Created at, Total Amount

**Purchase Items Table:**
Columns: #, Product Name, SKU/Variant, Quantity, Unit Price (৳), Subtotal (৳)
Footer row: Total Amount in bold

**Print Button:** `window.print()` on click, styled as btn-outline-secondary

After completing all 4 tasks, confirm:

- SupplierController has full CRUD with company_id scoping
- suppliers/index.blade.php has modal-based CRUD
- purchases/index.blade.php shows paginated list
- purchases/show.blade.php shows full purchase detail

```

---

## 🥈 SPRINT 2 PROMPT — Products Edit/Show + Branches Edit + Inventory
> **Goal:** Complete the Products and Branches modules. Both controllers are 100% ready — only views needed.

```

You are a Senior Laravel Engineer working on a multi-tenant SaaS POS system.

Tech stack: Laravel 11, Blade, Tabler UI (Bootstrap 5). Layout: `@extends('layouts.admin_master')`

---

### TASK 1: Build Products Edit View

File: `resources/views/company/products/edit.blade.php`

The controller `ProductController@edit` passes:

- `$product` — with `variants` loaded. Each variant's `attributes` is already decoded to array.
- `$categories`, `$brands`, `$units`, `$taxes` — all scoped to company

This view is almost identical to `company/products/create.blade.php`.
Copy the create view structure and make these changes:

1. Change form action to: `route('company.products.update', $product)`
2. Add `@method('PUT')` after `@csrf`
3. Change page title to "Edit Product: {{ $product->name }}"
4. Pre-fill the Basic Information fields using `old('name', $product->name)`, `old('category_id', $product->category_id)`, etc.
5. Pre-fill the `has_variants` checkbox: `{{ $product->has_variants ? 'checked' : '' }}`
6. For the variants section: instead of `$oldVariants` defaulting to one empty variant, loop over `$product->variants` as the default:
    ```php
    $oldVariants = old('variants') ?: $product->variants->map(fn($v) => [
        'id'            => $v->id,
        'sku'           => $v->sku,
        'barcode'       => $v->barcode,
        'unit_id'       => $v->unit_id,
        'tax_id'        => $v->tax_id,
        'cost_price'    => $v->cost_price,
        'selling_price' => $v->selling_price,
        'stock'         => optional($v->stock)->quantity ?? 0,
        'reorder_level' => $v->reorder_level,
        'attributes'    => $v->attributes ?? [],
    ])->toArray();
    ```
7. Add a hidden input inside each variant row: `<input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant['id'] ?? '' }}">`
8. Change submit button text to "Update Product"
9. Add a "Cancel" link back to `route('company.products.index')`

---

### TASK 2: Build Products Show View

File: `resources/views/company/products/show.blade.php`

The controller passes: `$product` with category, brand, variants.stock, variants.tax loaded.

Build a product detail page with:

**Page Header:**

- Title: product name, breadcrumb (Dashboard > Products > {name})
- "Edit Product" button → `route('company.products.edit', $product)`
- "Back to List" button → `route('company.products.index')`

**Product Info Card:**

- Name, Category, Brand, Description
- Status badge (Active/Inactive)
- Has Variants badge

**Variants & Stock Table:**
For each variant in `$product->variants`:
Columns: SKU, Barcode, Unit, Tax, Cost Price, Selling Price, Current Stock, Reorder Level, Attributes (display as badges)

**Stock Status:**

- If stock quantity == 0: badge `bg-danger` "Out of Stock"
- If stock quantity <= reorder_level: badge `bg-warning` "Low Stock"
- Else: badge `bg-success` "In Stock"

---

### TASK 3: Build Branches Edit View

File: `resources/views/company/branches/edit.blade.php`

The controller `BranchController@edit` passes: `$branch`, `$managers` (all company users)

Copy the structure from `company/branches/create.blade.php` and:

1. Change form action to: `route('company.branches.update', $branch->id)`
2. Add `@method('PUT')` after `@csrf`
3. Pre-fill all fields: name, address, phone, email, manager_id using `old('field', $branch->field)`
4. Add a Status field (select: active/inactive) pre-filled with `$branch->status`
5. Change title to "Edit Branch: {{ $branch->name }}"
6. Change submit button to "Update Branch"

---

### TASK 4: Implement InventoryController + Build Both Views

**4a. Update Controller:**
File: `app/Http/Controllers/Company/InventoryController.php`

Replace the stub with:

```php
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

public function lowStock()
{
    $companyId = Auth::user()->company_id;

    $lowStockItems = Stock::with(['variant.product.category', 'variant.unit', 'branch'])
        ->whereHas('variant.product', fn($q) => $q->where('company_id', $companyId))
        ->whereColumn('quantity', '<=', 'reorder_level')
        ->orderBy('quantity', 'asc')
        ->paginate(20);

    $outOfStockCount = Stock::whereHas('variant.product', fn($q) => $q->where('company_id', $companyId))
        ->where('quantity', 0)->count();

    $criticalCount = Stock::whereHas('variant.product', fn($q) => $q->where('company_id', $companyId))
        ->whereColumn('quantity', '<=', 'reorder_level')->where('quantity', '>', 0)->count();

    return view('company.inventory.low-stock', compact('lowStockItems', 'outOfStockCount', 'criticalCount'));
}

public function stockAdjust()
{
    $companyId = Auth::user()->company_id;
    $branches = Branch::where('company_id', $companyId)->get();
    $recentAdjustments = StockMovement::with(['variant.product', 'branch', 'user'])
        ->where('company_id', $companyId)
        ->where('type', 'adjustment')
        ->latest()
        ->take(10)
        ->get();
    return view('company.inventory.stock_adjust', compact('branches', 'recentAdjustments'));
}

public function storeAdjustment(Request $request)
{
    $companyId = Auth::user()->company_id;
    $request->validate([
        'variant_id'  => 'required|exists:product_variants,id',
        'branch_id'   => 'nullable|exists:branches,id',
        'type'        => 'required|in:add,subtract,set',
        'quantity'    => 'required|integer|min:1',
        'reason'      => 'nullable|string|max:500',
    ]);

    DB::beginTransaction();
    try {
        $stock = Stock::firstOrCreate(
            ['company_id' => $companyId, 'variant_id' => $request->variant_id, 'branch_id' => $request->branch_id],
            ['quantity' => 0, 'reorder_level' => 5]
        );

        $before = $stock->quantity;
        match($request->type) {
            'add'      => $stock->increment('quantity', $request->quantity),
            'subtract' => $stock->decrement('quantity', $request->quantity),
            'set'      => $stock->update(['quantity' => $request->quantity]),
        };
        $stock->refresh();

        StockMovement::create([
            'company_id' => $companyId,
            'variant_id' => $request->variant_id,
            'branch_id'  => $request->branch_id,
            'type'       => 'adjustment',
            'quantity'   => $request->quantity,
            'reference'  => 'Manual Adjustment: ' . ($request->reason ?? 'No reason given') . " (Before: {$before}, After: {$stock->quantity})",
            'user_id'    => Auth::id(),
        ]);

        DB::commit();
        return redirect()->route('company.inventory.stock-adjust')->with('success', 'Stock adjusted successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Adjustment failed: ' . $e->getMessage());
    }
}
```

Also add this route to `routes/web.php` inside the company group (after the existing inventory routes):

```php
Route::post('/inventory/stock-adjust', [CompanyInventoryController::class, 'storeAdjustment'])->name('inventory.stock-adjust.store');
```

**4b. Build Low Stock View:**
File: `resources/views/company/inventory/low-stock.blade.php`

Build a page with:

- Page title "Low Stock Alerts" with breadcrumb
- 2 stat cards: "Out of Stock" (danger) and "Critical / Low Stock" (warning)
- Table: Product Name, SKU, Category, Branch, Current Qty (badge colored by severity), Reorder Level, Shortage (reorder_level - quantity)
- Color coding: qty == 0 → red row, qty <= reorder_level → yellow row
- Pagination

**4c. Build Stock Adjustment View:**
File: `resources/views/company/inventory/stock_adjust.blade.php`

Build a page with:

- Page title "Stock Adjustment" with breadcrumb
- Adjustment Form Card:
    - Branch select (from $branches, with "Central Warehouse" as first option value="")
    - Product Variant select (use AJAX or a simple select2 — load all variants via a hidden JSON or a separate endpoint)
    - Adjustment Type: radio buttons (Add Stock / Subtract Stock / Set Exact Quantity)
    - Quantity input (number, min 1)
    - Reason textarea (optional)
    - Submit button "Apply Adjustment"
- Recent Adjustments Table (last 10):
  Columns: Date, Product, Branch, Type, Quantity, Reason, By

After completing all tasks, confirm:

- products/edit.blade.php pre-fills all variant data including hidden id fields
- products/show.blade.php shows variant/stock table with status badges
- branches/edit.blade.php pre-fills all branch fields
- InventoryController has lowStock(), stockAdjust(), storeAdjustment() methods
- Both inventory views are built with real data

```

---

## 🥉 SPRINT 3 PROMPT — Customers & Expenses Modules
> **Goal:** Build the CRM & Finance section from scratch (both controller + view).

```

You are a Senior Laravel Engineer working on a multi-tenant SaaS POS system.

Tech stack: Laravel 11, Blade, Tabler UI (Bootstrap 5). Layout: `@extends('layouts.admin_master')`
All queries MUST be scoped to: `Auth::user()->company_id`

---

### TASK 1: Build CustomerController (Full CRUD)

File: `app/Http/Controllers/Company/CustomerController.php`

The `customers` table has: `id, company_id, name, email, phone, address, notes, created_at, updated_at`

Implement:

**index():**

- Paginate 15, search on name/phone/email
- Pass total customer count and new this month count as stats
- Return view `company.customers.index`

**store(Request $request):**

- Validate: name (required), email (nullable, email, unique scoped to company), phone (nullable), address (nullable), notes (nullable)
- Create with company_id
- Return redirect with success

**update(Request $request, Customer $customer):**

- Security check: abort(403) if not same company
- Validate (unique email ignoring current)
- Update and redirect

**destroy(Customer $customer):**

- Security check
- Check if customer has sales: if yes, return back with error 'Cannot delete customer with sales history.'
- Delete and redirect

---

### TASK 2: Build Customers View

File: `resources/views/company/customers/index.blade.php`

Build a premium page with:

**Stats Row (3 cards):** Total Customers, New This Month, (optional: Total Revenue from customers)

**Search + Add Button row:**

- Search form (GET, name/phone/email)
- "Add Customer" button → opens Bootstrap modal

**Customers Table:**
Columns: #, Name, Phone, Email, Address, Notes, Joined Date, Actions (Edit modal + Delete)

**Add/Edit Modal:**

- Fields: Name\*, Email, Phone, Address, Notes
- JavaScript to populate on edit using data-\* attributes

**Empty State:** Friendly icon + message + "Add First Customer" button

---

### TASK 3: Build ExpenseController (Full CRUD)

File: `app/Http/Controllers/Company/ExpenseController.php`

The `expenses` table has: `id, company_id, branch_id (nullable), title, amount, category, expense_date, description, created_by (user_id), created_at, updated_at`

Implement:

**index():**

- Paginate 15, filter by date range (from/to) and branch_id
- Pass total expense amount for current month and all time
- Return view `company.expenses.index`

**store(Request $request):**

- Validate: title (required), amount (required, numeric, min:0), category (required), expense_date (required, date), branch_id (nullable, exists:branches,id), description (nullable)
- Create with company_id and created_by = Auth::id()
- Return redirect with success

**update(Request $request, Expense $expense):**

- Security check
- Validate and update

**destroy(Expense $expense):**

- Security check
- Delete and redirect

---

### TASK 4: Build Expenses View

File: `resources/views/company/expenses/index.blade.php`

Build a premium page with:

**Stats Row (3 cards):**

- Total Expenses (all time, ৳ formatted)
- This Month's Expenses (৳ formatted)
- Number of Expense Entries

**Filter Bar:**

- Date From, Date To, Branch select, Filter + Reset buttons

**"Add Expense" button** → opens Bootstrap modal

**Expenses Table:**
Columns: #, Title, Category (badge), Branch (or "Head Office"), Amount (৳), Date, Description, Added By, Actions (Edit + Delete)

**Add/Edit Modal:**

- Fields: Title*, Category* (select: Rent, Utilities, Salary, Transport, Marketing, Maintenance, Other), Amount*, Date*, Branch (optional), Description
- JavaScript populate on edit

**Pagination**

After completing all tasks, confirm:

- CustomerController has full CRUD with company_id scoping and sales history check before delete
- ExpenseController has full CRUD with company_id scoping
- Both views have modal-based CRUD, stats cards, search/filter, and empty states

```

---

## 🏁 SPRINT 4 PROMPT — Users, Stock Transfers & Settings
> **Goal:** Complete the Staff management, Stock Transfer module, and all Settings pages.

```

You are a Senior Laravel Engineer working on a multi-tenant SaaS POS system.

Tech stack: Laravel 11, Blade, Tabler UI (Bootstrap 5), Spatie Laravel Permission.
Layout: `@extends('layouts.admin_master')`

---

### TASK 1: Complete UserController — Add index() and assignRole()

File: `app/Http/Controllers/Company/UserController.php`

Add these two methods to the existing class (keep create() and store() as-is):

**index():**

```php
public function index()
{
    $companyId = auth()->user()->company_id;
    $users = User::where('company_id', $companyId)
        ->with(['roles', 'branch'])
        ->latest()
        ->paginate(15);
    $branches = Branch::where('company_id', $companyId)->get();
    return view('company.users.index', compact('users', 'branches'));
}
```

**assignRole(Request $request, User $user):**

```php
public function assignRole(Request $request, User $user)
{
    if ($user->company_id !== auth()->user()->company_id) abort(403);
    $request->validate(['role' => 'required|string']);
    $user->syncRoles([$request->role]);
    return redirect()->back()->with('success', 'Role updated for ' . $user->name);
}
```

---

### TASK 2: Build Users Index View

File: `resources/views/company/users/index.blade.php`

The controller passes: `$users` (paginated, with roles and branch), `$branches`

Build a premium page with:

**Page Header:**

- Title "Staff & Roles" with breadcrumb
- "Add New Staff" button → `route('company.users.create')`

**Stats Row (3 cards):**

- Total Staff, Active (count), Roles in use (unique roles count)

**Users Table:**
Columns: #, Avatar (initials circle), Name + Email, Role (badge, colored by role), Branch (or "All Branches"), Joined Date, Actions

**Actions per row:**

- "Change Role" button → opens a small Bootstrap modal with a role select dropdown
- Delete button (with confirm) — add destroy() method to controller if not present

**Change Role Modal:**

- Select dropdown with options: Company Admin, Branch Manager, Cashier
- Hidden input for user_id
- Form action: `route('company.users.assign-role', $user->id)` with `@method('PATCH')`

**Pagination**

---

### TASK 3: Build TransferController (Full Implementation)

File: `app/Http/Controllers/Company/TransferController.php`

Replace the empty class with full implementation.

The `stock_transfers` table has:
`id, company_id, from_branch_id (nullable), to_branch_id, transferred_by (user_id), transfer_date, status (pending/completed/cancelled), notes, created_at, updated_at`

The `stock_transfer_items` table has:
`id, transfer_id, variant_id, quantity`

Implement:

**index():**

- Get transfers paginated 15, with fromBranch, toBranch, transferredBy, items
- Return view `company.transfers.index`

**create():**

- Load branches (company scoped) and active product variants (with product)
- Return view `company.transfers.create`

**store(Request $request):**

- Validate: from_branch_id (nullable, exists:branches,id), to_branch_id (required, exists:branches,id, different from from_branch_id), transfer_date (required, date), items (required, array, min:1), items._.variant_id (required, exists), items._.quantity (required, integer, min:1), notes (nullable)
- DB::beginTransaction()
- Create StockTransfer record
- For each item:
    - Create StockTransferItem
    - Deduct stock from source (from_branch_id): `Stock::where(...)->decrement('quantity', $qty)` — abort if insufficient stock
    - Add stock to destination (to_branch_id): `Stock::updateOrCreate([...], ['quantity' => DB::raw('quantity + ' . $qty)])`
    - Create StockMovement records for both deduction and addition (type: 'transfer_out' and 'transfer_in')
- DB::commit()
- Redirect to transfers index with success

---

### TASK 4: Build Stock Transfer Views

**4a. Transfers Index:**
File: `resources/views/company/transfers/index.blade.php`

Build with:

- Page header "Stock Transfers" + "New Transfer" button
- Stats: Total Transfers, Completed, Pending
- Table: #, Date, From Branch (or "Central Warehouse"), To Branch, Items Count, Status (badge), Transferred By, Actions (View details)
- Pagination

**4b. Transfers Create:**
File: `resources/views/company/transfers/create.blade.php`

Build with:

- Page header "New Stock Transfer" + "Back to List" button
- Form fields:
    - From Branch (select, with "Central Warehouse / Head Office" as first option, value="")
    - To Branch (select, required — must be different from source)
    - Transfer Date (date input, default today)
    - Notes (textarea, optional)
- Dynamic Items Table (same pattern as purchases/create.blade.php):
    - Product Variant select (with product name + SKU)
    - Quantity input
    - Current Stock display (auto-fill via JS when variant selected)
    - Remove row button
    - "Add Item" button
- Submit button "Execute Transfer"
- JavaScript: auto-populate current stock when variant is selected (pass variants with stock as JSON)

---

### TASK 5: Build Settings Pages

**5a. Update CompanySettingController:**
File: `app/Http/Controllers/Company/CompanySettingController.php`

Replace with:

```php
public function profile()
{
    $company = Auth::user()->company;
    return view('company.settings.profile', compact('company'));
}

public function updateProfile(Request $request)
{
    $company = Auth::user()->company;
    $request->validate([
        'name'    => 'required|string|max:255',
        'email'   => 'nullable|email|max:255',
        'phone'   => 'nullable|string|max:20',
        'address' => 'nullable|string',
        'city'    => 'nullable|string|max:100',
        'country' => 'nullable|string|max:100',
        'logo'    => 'nullable|image|max:2048',
    ]);
    $data = $request->only(['name', 'email', 'phone', 'address', 'city', 'country']);
    if ($request->hasFile('logo')) {
        $data['logo'] = $request->file('logo')->store('logos', 'public');
    }
    $company->update($data);
    return redirect()->route('company.settings.profile')->with('success', 'Company profile updated.');
}

public function invoice()
{
    $company = Auth::user()->company;
    return view('company.settings.invoice', compact('company'));
}

public function updateInvoice(Request $request)
{
    $company = Auth::user()->company;
    $request->validate([
        'invoice_prefix' => 'nullable|string|max:10',
        'invoice_footer' => 'nullable|string|max:500',
        'show_logo'      => 'nullable|boolean',
        'currency_symbol'=> 'nullable|string|max:5',
    ]);
    $company->update($request->only(['invoice_prefix', 'invoice_footer', 'show_logo', 'currency_symbol']));
    return redirect()->route('company.settings.invoice')->with('success', 'Invoice settings updated.');
}
```

Also add these POST routes to `routes/web.php` inside the company settings prefix group:

```php
Route::post('/profile', [CompanySettingController::class, 'updateProfile'])->name('profile.update');
Route::post('/invoice', [CompanySettingController::class, 'updateInvoice'])->name('invoice.update');
```

**5b. Build Company Profile Settings View:**
File: `resources/views/company/settings/profile.blade.php`

Build with:

- Page title "Company Profile" with breadcrumb
- Form (POST to `route('company.settings.profile.update')`, enctype multipart):
    - Company Name\*, Email, Phone, Address, City, Country
    - Logo upload (show current logo preview if exists)
    - Save button

**5c. Build Invoice Settings View:**
File: `resources/views/company/settings/invoice.blade.php`

Build with:

- Page title "Invoice Settings" with breadcrumb
- Form (POST to `route('company.settings.invoice.update')`):
    - Invoice Prefix (e.g., "INV-")
    - Currency Symbol (e.g., "৳")
    - Show Logo on Invoice (toggle switch)
    - Invoice Footer Text (textarea)
    - Save button
- Live Preview Card: shows a mini invoice preview that updates as fields change (optional JS enhancement)

**5d. Build Attribute Settings View:**
File: `resources/views/company/settings/attributes/index.blade.php`

The controller (`Tenant\AttributeController@index`) passes: `$attributes` (with values)

Build with:

- Page title "Variant & Attribute Settings" with breadcrumb
- "Add Attribute" button → opens modal
- Attributes Table:
  Columns: #, Attribute Name, Values (shown as comma-separated badges), Actions (Edit + Delete)
- Add/Edit Modal:
    - Attribute Name input
    - Values textarea (comma or newline separated, e.g., "Red, Blue, Green")
    - Help text: "Enter values separated by commas or new lines"

---

### TASK 6: Build Announcements View

File: `resources/views/company/announcements/index.blade.php`

First update `AnnouncementController@index`:

```php
public function index()
{
    $announcements = \App\Models\Announcement::where('is_active', true)
        ->latest()
        ->paginate(10);
    return view('company.announcements.index', compact('announcements'));
}
```

Build the view with:

- Page title "Announcements" with breadcrumb
- Announcements displayed as cards (not a table):
  Each card: Title (h5), Content (text), Date (muted), Type badge (info/warning/danger based on type column)
- Empty state: "No announcements at this time."
- Pagination

After completing all tasks, confirm:

- UserController has index() and assignRole() methods
- users/index.blade.php shows staff list with role change modal
- TransferController has full index/create/store with stock deduction/addition logic
- Both transfer views are built
- CompanySettingController has profile/updateProfile/invoice/updateInvoice
- All 4 settings views are built (profile, invoice, attributes, announcements)

```

---

## 📋 FINAL VERIFICATION PROMPT
> **Run this after all sprints are complete to verify nothing was missed.**

```

You are a QA Engineer. Perform a final verification check on the Company Panel of this Laravel SaaS application.

For each of the following routes, confirm:

1. The controller method exists and has real logic (not `//`)
2. The view file exists and is not 0-byte
3. The view extends `layouts.admin_master`
4. All variables passed from controller are used in the view

Routes to verify:

- company.dashboard
- company.sales.index
- company.products.index / create / show / edit
- company.categories.index / create / edit
- company.inventory.low-stock / stock-adjust
- company.transfers.index / create
- company.purchases.index / create / show
- company.suppliers.index
- company.customers.index
- company.expenses.index
- company.branches.index / create / edit
- company.users.index / create
- company.reports.sales / stock
- company.settings.profile / invoice / attributes.index
- company.subscription.index
- company.announcements.index

For any route that still fails the check, report:

- Route name
- What is missing (controller method / view file / 0-byte view)
- Exact file path to fix

Output a final checklist table with ✅ or ❌ for each route.

```

---

## 🗒️ Notes for the Engineer

### Project Conventions (MUST follow)
1. **Always scope by company_id:** Every DB query in Company controllers must include `->where('company_id', Auth::user()->company_id)`
2. **Security check pattern:** For show/edit/update/destroy, always verify ownership: `if ($model->company_id !== Auth::user()->company_id) abort(403);`
3. **Layout:** All views must `@extends('layouts.admin_master')` and use `@section('content')`
4. **Icons:** Use Tabler Icons class format: `<i class="ti ti-{icon-name}"></i>`
5. **Flash messages:** Use `session('success')` and `session('error')` — the master layout already handles these
6. **Pagination:** Always use `->paginate(15)` not `->get()` for list pages
7. **Empty states:** Every table must have a `@empty` / `@forelse` with a friendly empty state
8. **Breadcrumbs:** Every page needs a breadcrumb: Dashboard > Section > Page
9. **CSRF:** Every POST/PUT/DELETE form must have `@csrf` and `@method('PUT'/'DELETE')` as appropriate
10. **Validation errors:** Use `@error('field')` pattern with `is-invalid` class on inputs

### File Structure Reference
```

app/Http/Controllers/Company/ ← All company controllers
resources/views/company/ ← All company views
dashboard.blade.php
sales/index.blade.php
products/index|create|edit|show.blade.php
categories/index|create|edit.blade.php
inventory/low-stock|stock_adjust.blade.php
transfers/index|create.blade.php
purchases/index|create|show.blade.php
suppliers/index.blade.php
customers/index.blade.php
expenses/index.blade.php
branches/index|create|edit|show.blade.php
users/index|create|assign_role.blade.php
reports/daily-sales|stock.blade.php
settings/profile|invoice.blade.php
settings/attributes/index.blade.php
subscription/index.blade.php
announcements/index.blade.php

```

```
