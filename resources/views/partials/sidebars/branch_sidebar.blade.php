<div class="sidenav-menu">

    <!-- Brand Logo -->
    <a href="{{ route('branch.dashboard') }}" class="logo">
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

            <li class="side-nav-title">Branch Panel</li>

            <!-- Dashboard -->
            <li class="side-nav-item">
                <a href="{{ route('branch.dashboard') }}"
                    class="side-nav-link {{ request()->routeIs('branch.dashboard') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-dashboard"></i></span>
                    <span class="menu-text"> Dashboard </span>
                </a>
            </li>

            <li class="side-nav-title mt-3">Sales & POS</li>

            <!-- POS Terminal -->
            <li class="side-nav-item">
                <a href="{{ route('branch.pos.index') }}"
                    class="side-nav-link {{ request()->routeIs('branch.pos.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-cash-register"></i></span>
                    <span class="menu-text"> POS Terminal </span>
                </a>
            </li>

            <!-- Sales History -->
            <li class="side-nav-item">
                <a href="{{ route('branch.sales.index') }}"
                    class="side-nav-link {{ request()->routeIs('branch.sales.index') || request()->routeIs('branch.sales.show') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-receipt"></i></span>
                    <span class="menu-text"> Sales History </span>
                </a>
            </li>

            <!-- Shift Management -->
            <li class="side-nav-item">
                <a href="{{ route('branch.shifts.index') }}"
                    class="side-nav-link {{ request()->routeIs('branch.shifts.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-clock"></i></span>
                    <span class="menu-text"> Shift Management </span>
                </a>
            </li>

            <!-- Sales Returns -->
            <li class="side-nav-item">
                <a href="{{ route('branch.sales-returns.index') }}"
                    class="side-nav-link {{ request()->routeIs('branch.sales-returns.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-receipt-refund"></i></span>
                    <span class="menu-text"> Sales Returns </span>
                </a>
            </li>

            <li class="side-nav-title mt-3">Inventory & Stock</li>

            <!-- Inventory & Stock Management (Collapsible Menu) -->
            @php
                $isBranchInventoryActive =
                    request()->routeIs('branch.inventory.*') || request()->routeIs('branch.purchases.*');
            @endphp
            <li class="side-nav-item {{ $isBranchInventoryActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarBranchInventory"
                    aria-expanded="{{ $isBranchInventoryActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isBranchInventoryActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-package"></i></span>
                    <span class="menu-text"> Inventory & Stock </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isBranchInventoryActive ? 'show' : '' }}" id="sidebarBranchInventory">
                    <ul class="sub-menu">

                        <!-- 🔥 নতুন যোগ করা হয়েছে: বাল্ক আইটেম রিসিভ ও সর্টিং -->
                        <li class="side-nav-item">
                            <a href="{{ route('branch.inventory.receive-sort') }}"
                                class="side-nav-link {{ request()->routeIs('branch.inventory.receive-sort') ? 'active' : '' }}">
                                <span class="menu-icon"><i class="ti ti-box-multiple"></i></span>
                                <span class="menu-text"> Receive & Sort Bulk </span>
                                <span class="badge bg-success-lt text-success ms-auto">New</span>
                            </a>
                        </li>

                        <!-- Current Stock -->
                        <li class="side-nav-item">
                            <a href="{{ route('branch.inventory.index') }}"
                                class="side-nav-link {{ request()->routeIs('branch.inventory.index') ? 'active' : '' }}">
                                <span class="menu-text"> Current Stock </span>
                            </a>
                        </li>

                        <!-- Adjust stock for damage/loss/audit -->
                        <li class="side-nav-item">
                            <a href="{{ route('branch.inventory.adjust') }}"
                                class="side-nav-link {{ request()->routeIs('branch.inventory.adjust') ? 'active' : '' }}">
                                <span class="menu-text"> Stock Adjustment </span>
                            </a>
                        </li>

                        <!-- Receive goods (Local purchase or Transfer from Head Office) -->
                        <li class="side-nav-item">
                            <a href="{{ route('branch.purchases.create') }}"
                                class="side-nav-link {{ request()->routeIs('branch.purchases.create') ? 'active' : '' }}">
                                <span class="menu-text"> New Purchase / Receive </span>
                            </a>
                        </li>

                        <!-- History of stock-in for this branch -->
                        <li class="side-nav-item">
                            <a href="{{ route('branch.purchases.index') }}"
                                class="side-nav-link {{ request()->routeIs('branch.purchases.index') ? 'active' : '' }}">
                                <span class="menu-text"> Purchase History </span>
                            </a>
                        </li>

                        <!-- Barcode Printing -->
                        <li class="side-nav-item">
                            <a href="{{ route('branch.inventory.barcode') }}"
                                class="side-nav-link {{ request()->routeIs('branch.inventory.barcode*') ? 'active' : '' }}">
                                <span class="menu-text"> Barcode Printing </span>
                            </a>
                        </li>

                    </ul>
                </div>
            </li>

            <li class="side-nav-title mt-3">Reports</li>

            <!-- Daily Sales Report (existing) -->
            <li class="side-nav-item">
                <a href="{{ route('branch.reports.daily-sales') }}"
                    class="side-nav-link {{ request()->routeIs('branch.reports.daily-sales') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-chart-bar"></i></span>
                    <span class="menu-text"> Daily Sales Report </span>
                </a>
            </li>

            <!-- Today's Report -->
            <li class="side-nav-item">
                <a href="{{ route('branch.reports.today') }}"
                    class="side-nav-link {{ request()->routeIs('branch.reports.today') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-calendar-stats"></i></span>
                    <span class="menu-text"> Today's Report </span>
                </a>
            </li>

            <!-- Inventory Report -->
            <li class="side-nav-item">
                <a href="{{ route('branch.reports.inventory') }}"
                    class="side-nav-link {{ request()->routeIs('branch.reports.inventory') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-clipboard-list"></i></span>
                    <span class="menu-text"> Inventory Report </span>
                </a>
            </li>

            <li class="side-nav-title mt-3">Account</li>

            <!-- Profile -->
            <li class="side-nav-item">
                <a href="{{ route('profile.edit') }}"
                    class="side-nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-user"></i></span>
                    <span class="menu-text"> My Profile </span>
                </a>
            </li>

        </ul>

    </div>
</div>
