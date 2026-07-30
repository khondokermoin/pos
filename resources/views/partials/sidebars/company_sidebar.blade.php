<div class="sidenav-menu">

    <!-- Brand Logo -->
    <a href="{{ route('company.dashboard') }}" class="logo">
        <span class="logo-light">
            <span class="logo-lg"><img src="{{ asset('frontend_assets/images/logo.png') }}" alt="logo"></span>
            <span class="logo-sm"><img src="{{ asset('frontend_assets/images/logo-sm.png') }}" alt="small logo"></span>
        </span>
        <span class="logo-dark">
            <span class="logo-lg"><img src="{{ asset('frontend_assets/images/logo-dark.png') }}" alt="dark logo"></span>
            <span class="logo-sm"><img src="{{ asset('frontend_assets/images/logo-sm.png') }}" alt="small logo"></span>
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <button class="button-sm-hover">
        <i class="ti ti-circle align-middle"></i>
    </button>

    <!-- Full Sidebar Menu Close Button -->
    <button class="button-close-fullsidebar">
        <i class="ti ti-x align-middle"></i>
    </button>

    <div data-simplebar>

        <!--- Sidenav Menu -->
        <ul class="side-nav">
            <li class="side-nav-title">Main</li>

            <!-- Dashboard -->
            <li class="side-nav-item">
                <a href="{{ route('company.dashboard') }}"
                    class="side-nav-link {{ request()->routeIs('company.dashboard') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-dashboard"></i></span>
                    <span class="menu-text"> Dashboard </span>
                </a>
            </li>

            <li class="side-nav-title mt-3">Sales & POS</li>

            <!-- Sales / Invoices (Company Level Overview) -->
            <li class="side-nav-item">
                <a href="{{ route('company.sales.index') }}"
                    class="side-nav-link {{ request()->routeIs('company.sales.index') || request()->routeIs('company.sales.show') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-receipt"></i></span>
                    <span class="menu-text"> All Sales </span>
                </a>
            </li>

            <!-- Quotations -->
            <li class="side-nav-item">
                <a href="{{ route('company.quotations.index') }}"
                    class="side-nav-link {{ request()->routeIs('company.quotations.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-file-description"></i></span>
                    <span class="menu-text"> Quotations </span>
                </a>
            </li>

            <!-- Sales Returns -->
            <li class="side-nav-item">
                <a href="{{ route('company.sales-returns.index') }}"
                    class="side-nav-link {{ request()->routeIs('company.sales-returns.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-receipt-refund"></i></span>
                    <span class="menu-text"> Sales Returns </span>
                </a>
            </li>

            <li class="side-nav-title mt-3">Inventory Management</li>

            <!-- Products & Categories -->
            @php
                $isInventoryActive =
                    request()->routeIs('company.products.*') ||
                    request()->routeIs('company.categories.*') ||
                    request()->routeIs('company.inventory.*') ||
                    request()->routeIs('company.transfers.*');
            @endphp
            <li class="side-nav-item {{ $isInventoryActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarProducts"
                    aria-expanded="{{ $isInventoryActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isInventoryActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-package"></i></span>
                    <span class="menu-text"> Products & Stock </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isInventoryActive ? 'show' : '' }}" id="sidebarProducts">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('company.products.index') }}"
                                class="side-nav-link {{ request()->routeIs('company.products.index') ? 'active' : '' }}">
                                <span class="menu-text">All Products</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.products.create') }}"
                                class="side-nav-link {{ request()->routeIs('company.products.create') ? 'active' : '' }}">
                                <span class="menu-text">Add Product</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.categories.index') }}"
                                class="side-nav-link {{ request()->routeIs('company.categories.*') ? 'active' : '' }}">
                                <span class="menu-text">Categories</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.inventory.low-stock') }}"
                                class="side-nav-link {{ request()->routeIs('company.inventory.low-stock') ? 'active' : '' }}">
                                <span class="menu-text">Low Stock Alerts</span>
                                <span class="badge bg-danger-lt text-danger ms-auto">!</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.inventory.stock-adjust') }}"
                                class="side-nav-link {{ request()->routeIs('company.inventory.stock-adjust') ? 'active' : '' }}">
                                <span class="menu-text">Stock Adjustment</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.transfers.index') }}"
                                class="side-nav-link {{ request()->routeIs('company.transfers.*') ? 'active' : '' }}">
                                <span class="menu-text">Stock Transfer</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-title mt-3">Purchasing</li>

            <!-- Purchases & Suppliers -->
            @php
                $isPurchasingActive =
                    request()->routeIs('company.purchases.*') ||
                    request()->routeIs('company.suppliers.*') ||
                    request()->routeIs('company.purchase-returns.*');
            @endphp
            <li class="side-nav-item {{ $isPurchasingActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarPurchasing"
                    aria-expanded="{{ $isPurchasingActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isPurchasingActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-shopping-cart"></i></span>
                    <span class="menu-text"> Purchasing </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isPurchasingActive ? 'show' : '' }}" id="sidebarPurchasing">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('company.purchases.index') }}"
                                class="side-nav-link {{ request()->routeIs('company.purchases.index') || request()->routeIs('company.purchases.show') ? 'active' : '' }}">
                                <span class="menu-text">All Purchases</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.purchases.create') }}"
                                class="side-nav-link {{ request()->routeIs('company.purchases.create') ? 'active' : '' }}">
                                <span class="menu-text">New Purchase</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.suppliers.index') }}"
                                class="side-nav-link {{ request()->routeIs('company.suppliers.*') ? 'active' : '' }}">
                                <span class="menu-text">Suppliers</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.purchase-returns.index') }}"
                                class="side-nav-link {{ request()->routeIs('company.purchase-returns.*') ? 'active' : '' }}">
                                <span class="menu-text">Purchase Returns</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-title mt-3">CRM & Finance</li>

            <!-- Customers -->
            <li class="side-nav-item">
                <a href="{{ route('company.customers.index') }}"
                    class="side-nav-link {{ request()->routeIs('company.customers.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-address-book"></i></span>
                    <span class="menu-text"> Customers </span>
                </a>
            </li>

            <!-- Expenses -->
            <li class="side-nav-item">
                <a href="{{ route('company.expenses.index') }}"
                    class="side-nav-link {{ request()->routeIs('company.expenses.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-receipt-2"></i></span>
                    <span class="menu-text"> Expenses </span>
                </a>
            </li>

            <!-- ============================================================ -->
            <!-- ACCOUNTING & FINANCE                                         -->
            <!-- ============================================================ -->
            <li class="side-nav-title mt-3">Accounting & Finance</li>

            <!-- Cash Book -->
            @php
                $isCashBookActive = request()->routeIs('company.cashbook.*');
            @endphp
            <li class="side-nav-item {{ $isCashBookActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarCashBook"
                    aria-expanded="{{ $isCashBookActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isCashBookActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-book"></i></span>
                    <span class="menu-text"> Cash Book </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isCashBookActive ? 'show' : '' }}" id="sidebarCashBook">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('company.cashbook.accounts') }}"
                                class="side-nav-link {{ request()->routeIs('company.cashbook.accounts') ? 'active' : '' }}">
                                <span class="menu-text">Accounts</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.cashbook.balances') }}"
                                class="side-nav-link {{ request()->routeIs('company.cashbook.balances') ? 'active' : '' }}">
                                <span class="menu-text">Balances</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.cashbook.transfers') }}"
                                class="side-nav-link {{ request()->routeIs('company.cashbook.transfers') ? 'active' : '' }}">
                                <span class="menu-text">Transfers</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.cashbook.history') }}"
                                class="side-nav-link {{ request()->routeIs('company.cashbook.history') ? 'active' : '' }}">
                                <span class="menu-text">History</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Loan Management -->
            @php
                $isLoanActive = request()->routeIs('company.loans.*');
            @endphp
            <li class="side-nav-item {{ $isLoanActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarLoans"
                    aria-expanded="{{ $isLoanActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isLoanActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-coin"></i></span>
                    <span class="menu-text"> Loan Management </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isLoanActive ? 'show' : '' }}" id="sidebarLoans">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('company.loans.authorities') }}"
                                class="side-nav-link {{ request()->routeIs('company.loans.authorities') ? 'active' : '' }}">
                                <span class="menu-text">Authorities</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.loans.loans') }}"
                                class="side-nav-link {{ request()->routeIs('company.loans.loans') ? 'active' : '' }}">
                                <span class="menu-text">Loans</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.loans.payments') }}"
                                class="side-nav-link {{ request()->routeIs('company.loans.payments') ? 'active' : '' }}">
                                <span class="menu-text">Payments</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Asset Management -->
            @php
                $isAssetActive = request()->routeIs('company.assets.*');
            @endphp
            <li class="side-nav-item {{ $isAssetActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarAssets"
                    aria-expanded="{{ $isAssetActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isAssetActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-building-warehouse"></i></span>
                    <span class="menu-text"> Asset Management </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isAssetActive ? 'show' : '' }}" id="sidebarAssets">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('company.assets.types') }}"
                                class="side-nav-link {{ request()->routeIs('company.assets.types') ? 'active' : '' }}">
                                <span class="menu-text">Types</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.assets.assets') }}"
                                class="side-nav-link {{ request()->routeIs('company.assets.assets') ? 'active' : '' }}">
                                <span class="menu-text">Assets</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- ============================================================ -->
            <!-- HR & PAYROLL                                                  -->
            <!-- ============================================================ -->
            <li class="side-nav-title mt-3">HR & Payroll</li>

            <!-- Payroll -->
            <li class="side-nav-item">
                <a href="{{ route('company.payroll.index') }}"
                    class="side-nav-link {{ request()->routeIs('company.payroll.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-cash"></i></span>
                    <span class="menu-text"> Payroll </span>
                </a>
            </li>

            <!-- Employees (collapsible) -->
            @php
                $isHrActive = request()->routeIs('company.employees.*') || request()->routeIs('company.departments.*');
            @endphp
            <li class="side-nav-item {{ $isHrActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarEmployees"
                    aria-expanded="{{ $isHrActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isHrActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-id-badge"></i></span>
                    <span class="menu-text"> Employees </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isHrActive ? 'show' : '' }}" id="sidebarEmployees">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('company.departments.index') }}"
                                class="side-nav-link {{ request()->routeIs('company.departments.*') ? 'active' : '' }}">
                                <span class="menu-text">Departments</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.employees.index') }}"
                                class="side-nav-link {{ request()->routeIs('company.employees.index') || request()->routeIs('company.employees.create') || request()->routeIs('company.employees.edit') ? 'active' : '' }}">
                                <span class="menu-text">Employee List</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.employees.increments') }}"
                                class="side-nav-link {{ request()->routeIs('company.employees.increments') ? 'active' : '' }}">
                                <span class="menu-text">Increments</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-title mt-3">Branch & Operations</li>

            <!-- Branches -->
            <li class="side-nav-item">
                <a href="{{ route('company.branches.index') }}"
                    class="side-nav-link {{ request()->routeIs('company.branches.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-building-store"></i></span>
                    <span class="menu-text"> Branches </span>
                </a>
            </li>

            <!-- Staff / Users -->
            <li class="side-nav-item">
                <a href="{{ route('company.users.index') }}"
                    class="side-nav-link {{ request()->routeIs('company.users.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-users"></i></span>
                    <span class="menu-text"> Staff & Roles </span>
                </a>
            </li>

            <li class="side-nav-title mt-3">Reports</li>

            <!-- Company Reports -->
            <li class="side-nav-item">
                <a href="{{ route('company.reports.sales') }}"
                    class="side-nav-link {{ request()->routeIs('company.reports.sales') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-chart-bar"></i></span>
                    <span class="menu-text"> Sales Report </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('company.reports.stock') }}"
                    class="side-nav-link {{ request()->routeIs('company.reports.stock') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-chart-pie"></i></span>
                    <span class="menu-text"> Stock Report </span>
                </a>
            </li>

            <!-- Balance Sheet -->
            <li class="side-nav-item">
                <a href="{{ route('company.reports.balance-sheet') }}"
                    class="side-nav-link {{ request()->routeIs('company.reports.balance-sheet') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-scale"></i></span>
                    <span class="menu-text"> Balance Sheet </span>
                </a>
            </li>

            <!-- Profit / Loss -->
            <li class="side-nav-item">
                <a href="{{ route('company.reports.profit-loss') }}"
                    class="side-nav-link {{ request()->routeIs('company.reports.profit-loss') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-trending-up"></i></span>
                    <span class="menu-text"> Profit / Loss </span>
                </a>
            </li>

            <!-- Expense Report -->
            <li class="side-nav-item">
                <a href="{{ route('company.reports.expenses') }}"
                    class="side-nav-link {{ request()->routeIs('company.reports.expenses') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-report-money"></i></span>
                    <span class="menu-text"> Expense Report </span>
                </a>
            </li>

            <!-- Supplier Payable -->
            <li class="side-nav-item">
                <a href="{{ route('company.reports.supplier-payable') }}"
                    class="side-nav-link {{ request()->routeIs('company.reports.supplier-payable') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-arrow-bar-down"></i></span>
                    <span class="menu-text"> Supplier Payable </span>
                </a>
            </li>

            <!-- Client Receivable -->
            <li class="side-nav-item">
                <a href="{{ route('company.reports.client-receivable') }}"
                    class="side-nav-link {{ request()->routeIs('company.reports.client-receivable') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-arrow-bar-up"></i></span>
                    <span class="menu-text"> Client Receivable </span>
                </a>
            </li>

            <li class="side-nav-title mt-3">Settings & Account</li>

            <!-- Settings & Subscription -->
            @php
                $isSettingsActive =
                    request()->routeIs('company.settings.*') ||
                    request()->routeIs('company.subscription.*') ||
                    request()->routeIs('company.announcements.*') ||
                    request()->routeIs('company.settings.attributes.*');
            @endphp
            <li class="side-nav-item {{ $isSettingsActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarSettings"
                    aria-expanded="{{ $isSettingsActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isSettingsActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-settings"></i></span>
                    <span class="menu-text"> Settings </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isSettingsActive ? 'show' : '' }}" id="sidebarSettings">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('company.settings.profile') }}"
                                class="side-nav-link {{ request()->routeIs('company.settings.profile') ? 'active' : '' }}">
                                <span class="menu-text">Company Profile</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.settings.invoice') }}"
                                class="side-nav-link {{ request()->routeIs('company.settings.invoice') ? 'active' : '' }}">
                                <span class="menu-text">Invoice Settings</span>
                            </a>
                        </li>
                        @can('manage attributes')
                            <li class="side-nav-item">
                                <a href="{{ route('company.settings.attributes.index') }}"
                                    class="side-nav-link {{ request()->routeIs('company.settings.attributes.*') ? 'active' : '' }}">
                                    <span class="menu-text">Variant & Attribute Settings</span>
                                </a>
                            </li>
                        @endcan
                        <li class="side-nav-item">
                            <a href="{{ route('company.subscription.index') }}"
                                class="side-nav-link {{ request()->routeIs('company.subscription.*') ? 'active' : '' }}">
                                <span class="menu-text">My Subscription</span>
                                @php
                                    $activeSub = Auth::user()->company?->subscription;
                                @endphp
                                @if ($activeSub && $activeSub->daysRemaining() <= 7 && $activeSub->daysRemaining() > 0)
                                    <span class="badge bg-danger ms-auto">{{ $activeSub->daysRemaining() }}d</span>
                                @endif
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('company.announcements.index') }}"
                                class="side-nav-link {{ request()->routeIs('company.announcements.*') ? 'active' : '' }}">
                                <span class="menu-text">Announcements</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- My Profile -->
            <li class="side-nav-item">
                <a href="{{ route('profile.edit') }}"
                    class="side-nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-user-circle"></i></span>
                    <span class="menu-text"> My Profile </span>
                </a>
            </li>

        </ul>

    </div>
</div>
