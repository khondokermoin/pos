<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;

// ==========================================
// Super Admin Controllers
// ==========================================
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboard;
use App\Http\Controllers\SuperAdmin\CompanyController;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\SubscriptionController;
use App\Http\Controllers\SuperAdmin\TransactionController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\SettingController;
use App\Http\Controllers\SuperAdmin\SystemController;

// Super Admin Controllers (master data / modules)
use App\Http\Controllers\SuperAdmin\BusinessTypeController;
use App\Http\Controllers\SuperAdmin\BusinessModuleController;
use App\Http\Controllers\SuperAdmin\GlobalCategoryController;
use App\Http\Controllers\SuperAdmin\GlobalUnitController;
use App\Http\Controllers\SuperAdmin\GlobalTaxController;
use App\Http\Controllers\SuperAdmin\GlobalAttributeController;
use App\Http\Controllers\SuperAdmin\InvoiceTemplateController;
use App\Http\Controllers\SuperAdmin\BarcodeSettingController;
use App\Http\Controllers\SuperAdmin\EmailTemplateController;
use App\Http\Controllers\SuperAdmin\AddonController;
use App\Http\Controllers\SuperAdmin\AddonMarketplaceController;
use App\Http\Controllers\SuperAdmin\SupportTicketController;
use App\Http\Controllers\SuperAdmin\AnnouncementController;
use App\Http\Controllers\SuperAdmin\ImpersonateController;
use App\Http\Controllers\SuperAdmin\ReportController as SuperAdminReportController;

// ==========================================
// Company Controllers
// ==========================================
use App\Http\Controllers\Company\DashboardController as CompanyDashboard;
use App\Http\Controllers\Company\BranchController;
use App\Http\Controllers\Company\UserController as CompanyUserController;
use App\Http\Controllers\Company\ProductController;
use App\Http\Controllers\Company\CategoryController;
use App\Http\Controllers\Company\PurchaseController;
use App\Http\Controllers\Company\CustomerController;
use App\Http\Controllers\Company\SupplierController;
use App\Http\Controllers\Company\ExpenseController;
use App\Http\Controllers\Company\ReportController as CompanyReportController;

// Company Controllers added based on Sidebar requirements
use App\Http\Controllers\Company\SaleController as CompanySaleController;
use App\Http\Controllers\Company\InventoryController as CompanyInventoryController;
use App\Http\Controllers\Company\CompanySettingController;
use App\Http\Controllers\Company\SubscriptionController as CompanySubscriptionController;
use App\Http\Controllers\Company\AnnouncementController as CompanyAnnouncementController;
use App\Http\Controllers\Company\TransferController;

// ERP Expansion Controllers
use App\Http\Controllers\Company\QuotationController;
use App\Http\Controllers\Company\SalesReturnController;
use App\Http\Controllers\Company\PurchaseReturnController;
use App\Http\Controllers\Company\CashBookController;
use App\Http\Controllers\Company\LoanController;
use App\Http\Controllers\Company\AssetController;
use App\Http\Controllers\Company\DepartmentController;
use App\Http\Controllers\Company\EmployeeController;
use App\Http\Controllers\Company\PayrollController;

// ==========================================
// Branch Controllers
// ==========================================
use App\Http\Controllers\Branch\DashboardController as BranchDashboard;
use App\Http\Controllers\Branch\InventoryController;
use App\Http\Controllers\Branch\PosController;
use App\Http\Controllers\Branch\SaleController;
use App\Http\Controllers\Branch\ShiftController;
use App\Http\Controllers\Branch\StockAdjustmentController;
use App\Http\Controllers\Branch\PurchaseController as BranchPurchaseController;
use App\Http\Controllers\Branch\SortingController;
use App\Http\Controllers\Branch\ReportController as BranchReportController;
use App\Http\Controllers\Branch\SalesReturnController as BranchSalesReturnController;
use App\Http\Controllers\Branch\BarcodeController as BranchBarcodeController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
})->middleware(['web', 'inertia']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// ==========================================
// 1. Super Admin Routes (SaaS Owner)
// ==========================================
Route::middleware(['auth', 'verified', 'role:Super Admin'])
    ->prefix('super-admin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/dashboard', [SuperAdminDashboard::class, 'index'])->name('dashboard');

        // SaaS Management
        Route::resource('/companies', CompanyController::class);
        Route::post('/companies/{company}/impersonate', [CompanyController::class, 'impersonate'])->name('companies.impersonate');
        Route::resource('/plans', PlanController::class);
        Route::resource('/transactions', TransactionController::class)->only(['index']);

        // Subscription Routes
        Route::get('subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
        Route::post('subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::resource('subscriptions', SubscriptionController::class)->only(['index', 'show']);
        Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::post('subscriptions/{subscription}/suspend', [SubscriptionController::class, 'suspend'])->name('subscriptions.suspend');
        Route::post('subscriptions/{subscription}/reactivate', [SubscriptionController::class, 'reactivate'])->name('subscriptions.reactivate');
        Route::post('subscriptions/{subscription}/extend', [SubscriptionController::class, 'extend'])->name('subscriptions.extend');

        // Platform Administration
        Route::resource('users', SuperAdminUserController::class)->except(['show']);
        Route::resource('roles', RoleController::class)->except(['show']);

        // Global Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/general', [SettingController::class, 'general'])->name('general');
            Route::post('/general', [SettingController::class, 'update'])->name('general.update');
            Route::get('/payment', [SettingController::class, 'payment'])->name('payment');
            Route::post('/payment', [SettingController::class, 'update'])->name('payment.update');
            Route::get('/email', [SettingController::class, 'email'])->name('email');
            Route::post('/email', [SettingController::class, 'update'])->name('email.update');
        });

        // System & Security
        Route::prefix('system')->name('system.')->group(function () {
            Route::get('/logs', [SystemController::class, 'logs'])->name('logs');
            Route::get('/backup', [SystemController::class, 'backup'])->name('backup');
            Route::post('/backup/generate', [SystemController::class, 'generateBackup'])->name('backup.generate');
            Route::get('/backup/download/{filename}', [SystemController::class, 'downloadBackup'])->name('backup.download')->where('filename', '[a-zA-Z0-9_\-.]+');
            Route::delete('/backup/{filename}', [SystemController::class, 'deleteBackup'])->name('backup.delete')->where('filename', '[a-zA-Z0-9_\-.]+');
            Route::get('/info', [SystemController::class, 'info'])->name('info');
            Route::post('/cache-clear', [SystemController::class, 'cacheClear'])->name('cache-clear');
            Route::get('/update', [SystemController::class, 'update'])->name('update');
            Route::post('/update', [SystemController::class, 'runUpdate'])->name('update.run');
        });

        // Global Master Data
        Route::resource('/business-types', BusinessTypeController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::resource('/business-modules', BusinessModuleController::class)->except(['create', 'edit', 'show']);
        Route::resource('/global-categories', GlobalCategoryController::class)->except(['create', 'edit', 'show']);
        Route::resource('/global-units', GlobalUnitController::class)->except(['create', 'edit', 'show']);
        Route::resource('/global-taxes', GlobalTaxController::class)->except(['create', 'edit', 'show']);
        Route::resource('/global-attributes', GlobalAttributeController::class)->except(['create', 'edit', 'show']);

        // POS & Customization
        Route::resource('/invoice-templates', InvoiceTemplateController::class)->except(['create', 'edit', 'show']);
        Route::patch('/invoice-templates/{invoiceTemplate}/set-default', [InvoiceTemplateController::class, 'setDefault'])->name('invoice-templates.set-default');
        // ── Invoice Template Preview (must be declared BEFORE the resource to avoid route conflicts)
        Route::get('/invoice-templates/{invoiceTemplate}/preview', [InvoiceTemplateController::class, 'preview'])->name('invoice-templates.preview');

        Route::resource('/barcode-settings', BarcodeSettingController::class)->except(['create', 'edit', 'show']);
        Route::patch('/barcode-settings/{barcodeSetting}/set-default', [BarcodeSettingController::class, 'setDefault'])->name('barcode-settings.set-default');

        // ── Email Template Preview — declared BEFORE the resource to avoid wildcard conflicts
        Route::get('/email-templates/{emailTemplate}/preview', [EmailTemplateController::class, 'preview'])->name('email-templates.preview');
        Route::resource('/email-templates', EmailTemplateController::class)->except(['create', 'edit', 'show']);

        // Company Module Access Management
        Route::get('/companies/{company}/modules', [BusinessModuleController::class, 'companyModules'])->name('companies.modules');
        Route::post('/companies/{company}/modules', [BusinessModuleController::class, 'saveCompanyModules'])->name('companies.modules.save');

        // Addons
        Route::get('/addons/marketplace', [AddonMarketplaceController::class, 'index'])->name('addons.marketplace');
        Route::resource('/addons', AddonController::class)->except(['create', 'edit', 'show']);

        // Helpdesk & Support Tickets (fully implemented)
        Route::resource('/support-tickets', SupportTicketController::class)->except(['create', 'edit']);
        Route::resource('/announcements', AnnouncementController::class)->except(['create', 'edit', 'show']);
        Route::get('/tenants', [ImpersonateController::class, 'index'])->name('tenants.index');

        // Global Reports
        Route::get('/reports', [SuperAdminReportController::class, 'index'])->name('reports.index');
    });

// ==========================================
// 1b. Impersonation Exit (Super Admin → Company)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/impersonate/leave', [CompanyController::class, 'leaveImpersonation'])->name('impersonate.leave');
});

// ==========================================
// 1c. Branch Impersonation Exit
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/branch/impersonate/leave', [BranchController::class, 'leaveBranchImpersonation'])->name('branch.impersonate.leave');
});

// ==========================================
// 2. Company Admin Routes (Shop Owner)
// ==========================================
Route::middleware(['auth', 'verified', 'role:Company Admin', 'tenant.access', 'subscription.check'])
    ->prefix('company')
    ->name('company.')
    ->group(function () {
        Route::get('/dashboard', [CompanyDashboard::class, 'index'])->name('dashboard');

        // Sales & Invoices (Company Level Overview)
        Route::get('/sales', [CompanySaleController::class, 'index'])->name('sales.index');

        // Branch & Staff Management
        Route::resource('/branches', BranchController::class);
        Route::resource('/users', CompanyUserController::class)->except(['show']);
        Route::patch('/users/{user}/assign-role', [CompanyUserController::class, 'assignRole'])->name('users.assign-role');

        // Inventory Master Data
        Route::resource('/products', ProductController::class);
        Route::resource('/categories', CategoryController::class);

        // Inventory Operations
        Route::get('/inventory/low-stock', [CompanyInventoryController::class, 'lowStock'])->name('inventory.low-stock');
        Route::get('/inventory/stock-adjust', [CompanyInventoryController::class, 'stockAdjust'])->name('inventory.stock-adjust');
        Route::post('/inventory/stock-adjust', [CompanyInventoryController::class, 'storeAdjustment'])->name('inventory.stock-adjust.store');

        // Stock Transfers
        Route::resource('/transfers', TransferController::class)->only(['index', 'create', 'store']);

        // Purchasing & Suppliers
        Route::resource('/purchases', PurchaseController::class);
        Route::resource('/suppliers', SupplierController::class)->except(['create', 'edit', 'show']);

        // Customers & Expenses
        Route::resource('/customers', CustomerController::class)->except(['create', 'edit', 'show']);
        Route::resource('/expenses', ExpenseController::class)->except(['create', 'edit', 'show']);

        // Company-level Reports
        Route::get('/reports/sales', [CompanyReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/stock', [CompanyReportController::class, 'stock'])->name('reports.stock');

        // Settings & Account
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/profile', [CompanySettingController::class, 'profile'])->name('profile');
            Route::post('/profile', [CompanySettingController::class, 'updateProfile'])->name('profile.update');
            Route::get('/invoice', [CompanySettingController::class, 'invoice'])->name('invoice');
            Route::post('/invoice', [CompanySettingController::class, 'updateInvoice'])->name('invoice.update');
            Route::resource('/attributes', \App\Http\Controllers\Tenant\AttributeController::class)->except(['create', 'edit', 'show']);
        });

        // Branch Impersonation
        Route::post('/branches/{branch}/impersonate', [BranchController::class, 'impersonate'])->name('branches.impersonate');

        // Subscription & Announcements
        Route::get('/subscription', [CompanySubscriptionController::class, 'index'])->name('subscription.index');
        Route::get('/subscription/plans', [CompanySubscriptionController::class, 'showPlans'])->name('subscription.plans');
        Route::post('/subscription/subscribe/{plan}', [CompanySubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
        Route::get('/subscription/invoice/{invoiceNumber}/download', [CompanySubscriptionController::class, 'downloadInvoice'])->name('subscription.invoice.download');
        Route::get('/announcements', [CompanyAnnouncementController::class, 'index'])->name('announcements.index');

        // ── ERP: Quotations ────────────────────────────────────────────────
        Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
        Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
        Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
        Route::get('/quotations/{id}', [QuotationController::class, 'show'])->name('quotations.show');
        Route::patch('/quotations/{id}/status', [QuotationController::class, 'updateStatus'])->name('quotations.update-status');
        Route::delete('/quotations/{id}', [QuotationController::class, 'destroy'])->name('quotations.destroy');

        // ── ERP: Sales Returns ─────────────────────────────────────────────
        Route::get('/sales-returns', [SalesReturnController::class, 'index'])->name('sales-returns.index');
        Route::get('/sales-returns/create', [SalesReturnController::class, 'create'])->name('sales-returns.create');
        Route::post('/sales-returns', [SalesReturnController::class, 'store'])->name('sales-returns.store');
        Route::get('/sales-returns/{id}', [SalesReturnController::class, 'show'])->name('sales-returns.show');
        Route::delete('/sales-returns/{id}', [SalesReturnController::class, 'destroy'])->name('sales-returns.destroy');
        Route::get('/sales-returns/sale/{saleId}/items', [SalesReturnController::class, 'getSaleItems'])->name('sales-returns.sale-items');

        // ── ERP: Purchase Returns ──────────────────────────────────────────
        Route::get('/purchase-returns', [PurchaseReturnController::class, 'index'])->name('purchase-returns.index');
        Route::get('/purchase-returns/create', [PurchaseReturnController::class, 'create'])->name('purchase-returns.create');
        Route::post('/purchase-returns', [PurchaseReturnController::class, 'store'])->name('purchase-returns.store');
        Route::get('/purchase-returns/{id}', [PurchaseReturnController::class, 'show'])->name('purchase-returns.show');
        Route::delete('/purchase-returns/{id}', [PurchaseReturnController::class, 'destroy'])->name('purchase-returns.destroy');
        Route::get('/purchase-returns/purchase/{purchaseId}/items', [PurchaseReturnController::class, 'getPurchaseItems'])->name('purchase-returns.purchase-items');

        // ── ERP: Cash Book ─────────────────────────────────────────────────
        Route::prefix('cashbook')->name('cashbook.')->group(function () {
            Route::get('/accounts', [CashBookController::class, 'accounts'])->name('accounts');
            Route::post('/accounts', [CashBookController::class, 'storeAccount'])->name('accounts.store');
            Route::delete('/accounts/{id}', [CashBookController::class, 'destroyAccount'])->name('accounts.destroy');
            Route::get('/balances', [CashBookController::class, 'balances'])->name('balances');
            Route::get('/transfers', [CashBookController::class, 'transfers'])->name('transfers');
            Route::post('/transfers', [CashBookController::class, 'storeTransfer'])->name('transfers.store');
            Route::get('/history', [CashBookController::class, 'history'])->name('history');
        });

        // ── ERP: Loan Management ───────────────────────────────────────────
        Route::prefix('loans')->name('loans.')->group(function () {
            Route::get('/authorities', [LoanController::class, 'authorities'])->name('authorities');
            Route::post('/authorities', [LoanController::class, 'storeAuthority'])->name('authorities.store');
            Route::delete('/authorities/{id}', [LoanController::class, 'destroyAuthority'])->name('authorities.destroy');
            Route::get('/loans', [LoanController::class, 'loans'])->name('loans');
            Route::post('/loans', [LoanController::class, 'storeLoan'])->name('loans.store');
            Route::delete('/loans/{id}', [LoanController::class, 'destroyLoan'])->name('loans.destroy');
            Route::get('/payments', [LoanController::class, 'payments'])->name('payments');
            Route::post('/payments', [LoanController::class, 'storePayment'])->name('payments.store');
        });

        // ── ERP: Asset Management ──────────────────────────────────────────
        Route::prefix('assets')->name('assets.')->group(function () {
            Route::get('/types', [AssetController::class, 'types'])->name('types');
            Route::post('/types', [AssetController::class, 'storeType'])->name('types.store');
            Route::delete('/types/{id}', [AssetController::class, 'destroyType'])->name('types.destroy');
            Route::get('/assets', [AssetController::class, 'assets'])->name('assets');
            Route::post('/assets', [AssetController::class, 'storeAsset'])->name('assets.store');
            Route::delete('/assets/{id}', [AssetController::class, 'destroyAsset'])->name('assets.destroy');
        });

        // ── ERP: HR — Departments ──────────────────────────────────────────
        Route::resource('/departments', DepartmentController::class)->except(['show']);

        // ── ERP: HR — Employees & Increments ──────────────────────────────
        Route::resource('/employees', EmployeeController::class);
        Route::get('/employees-increments', [EmployeeController::class, 'increments'])->name('employees.increments');
        Route::post('/employees-increments', [EmployeeController::class, 'storeIncrement'])->name('employees.increments.store');

        // ── ERP: Payroll ───────────────────────────────────────────────────
        Route::prefix('payroll')->name('payroll.')->group(function () {
            Route::get('/', [PayrollController::class, 'index'])->name('index');
            Route::post('/generate', [PayrollController::class, 'generate'])->name('generate');
            Route::post('/{id}/mark-paid', [PayrollController::class, 'markPaid'])->name('mark-paid');
            Route::get('/{id}/payslip', [PayrollController::class, 'payslip'])->name('payslip');
            Route::delete('/{id}', [PayrollController::class, 'destroy'])->name('destroy');
        });

        // ── ERP: Advanced Financial Reports ───────────────────────────────
        Route::get('/reports/balance-sheet', [CompanyReportController::class, 'balanceSheet'])->name('reports.balance-sheet');
        Route::get('/reports/profit-loss', [CompanyReportController::class, 'profitLoss'])->name('reports.profit-loss');
        Route::get('/reports/expenses', [CompanyReportController::class, 'expenses'])->name('reports.expenses');
        Route::get('/reports/supplier-payable', [CompanyReportController::class, 'supplierPayable'])->name('reports.supplier-payable');
        Route::get('/reports/client-receivable', [CompanyReportController::class, 'clientReceivable'])->name('reports.client-receivable');
    });

// ==========================================
// Payment Gateway Routes (PUBLIC)
// ==========================================
Route::match(['get', 'post'], '/company/subscription/payment/callback', [CompanySubscriptionController::class, 'paymentCallback'])->name('payment.callback');
Route::get('/payment/result', [CompanySubscriptionController::class, 'paymentResult'])->name('payment.result');

// ==========================================
// 3. Branch Routes (Manager / Salesman)
// ==========================================
Route::middleware(['auth', 'verified', 'role:Manager|Salesman', 'tenant.access'])
    ->prefix('branch')
    ->name('branch.')
    ->group(function () {
        Route::get('/dashboard', [BranchDashboard::class, 'index'])->name('dashboard');

        // Inventory
        Route::resource('/inventory', InventoryController::class)->except(['create', 'edit', 'show']);
        Route::get('/inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::post('/inventory/adjust', [InventoryController::class, 'storeAdjustment'])->name('inventory.adjust.store');

        // Branch Sorting
        Route::get('/inventory/receive-sort', [SortingController::class, 'receiveSort'])->name('inventory.receive-sort');
        Route::post('/inventory/sort-items', [SortingController::class, 'storeSortedItems'])->name('inventory.sort-items');
        Route::get('/inventory/sorting-history', [SortingController::class, 'history'])->name('inventory.sorting-history');
        Route::get('/inventory/sorting-history/{id}', [SortingController::class, 'showHistory'])->name('inventory.sorting-history.show');

        // Barcode Printing (NEW)
        Route::get('/inventory/barcode', [BranchBarcodeController::class, 'index'])->name('inventory.barcode');
        Route::post('/inventory/barcode/print', [BranchBarcodeController::class, 'print'])->name('inventory.barcode.print');

        // Branch Purchases
        Route::resource('/purchases', BranchPurchaseController::class)->only(['index', 'create', 'store']);

        // POS Terminal
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/products', [PosController::class, 'products'])->name('pos.products');
        Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::get('/pos/invoice/{sale}/print', [PosController::class, 'printInvoice'])->name('pos.invoice-print');
        Route::post('/pos/customers/quick-create', [PosController::class, 'quickCreateCustomer'])->name('pos.customers.quick-create');
        Route::post('/pos/hold', [PosController::class, 'holdOrder'])->name('pos.hold');
        Route::get('/pos/held-orders', [PosController::class, 'heldOrders'])->name('pos.held-orders');
        Route::delete('/pos/held-orders/{heldOrder}', [PosController::class, 'deleteHeldOrder'])->name('pos.held-orders.delete');

        // Shift Management
        Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
        Route::get('/shifts/create', [ShiftController::class, 'create'])->name('shifts.create');
        Route::post('/shifts', [ShiftController::class, 'store'])->name('shifts.store');
        Route::post('/shifts/{shift}/close', [ShiftController::class, 'close'])->name('shifts.close');

        // Sales History
        Route::resource('/sales', SaleController::class)->only(['index', 'show']);

        // Sales Returns (NEW)
        Route::get('/sales-returns', [BranchSalesReturnController::class, 'index'])->name('sales-returns.index');
        Route::get('/sales-returns/create', [BranchSalesReturnController::class, 'create'])->name('sales-returns.create');
        Route::post('/sales-returns', [BranchSalesReturnController::class, 'store'])->name('sales-returns.store');
        Route::get('/sales-returns/{id}', [BranchSalesReturnController::class, 'show'])->name('sales-returns.show');
        Route::get('/sales-returns/sale/{saleId}/items', [BranchSalesReturnController::class, 'getSaleItems'])->name('sales-returns.sale-items');

        // Reports
        Route::get('/reports/daily-sales', [BranchReportController::class, 'dailySales'])->name('reports.daily-sales');
        Route::get('/reports/today', [BranchReportController::class, 'today'])->name('reports.today');
        Route::get('/reports/inventory', [BranchReportController::class, 'inventory'])->name('reports.inventory');
    });

// ==========================================
// 4. Global Auth Routes (Profile)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// 5. Smart Fallback Dashboard
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();

        if ($user->hasRole('Super Admin')) return redirect()->route('superadmin.dashboard');
        if ($user->hasRole('Company Admin')) return redirect()->route('company.dashboard');
        if ($user->hasRole('Manager') || $user->hasRole('Salesman')) return redirect()->route('branch.dashboard');

        abort(403, 'You are logged in, but you don\'t have any specific SaaS role assigned yet! Please contact the Super Admin.');
    })->name('dashboard');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/frontend.php';
