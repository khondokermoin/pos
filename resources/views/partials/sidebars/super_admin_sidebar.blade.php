<div class="sidenav-menu">

    <!-- Brand Logo -->
    <a href="{{ route('superadmin.dashboard') }}" class="logo">
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
            <li class="side-nav-title">Super Admin Panel</li>

            <!-- Dashboard -->
            <li class="side-nav-item">
                <a href="{{ route('superadmin.dashboard') }}"
                    class="side-nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-dashboard"></i></span>
                    <span class="menu-text"> Dashboard </span>
                </a>
            </li>

            <li class="side-nav-title mt-2">SaaS Management</li>

            <!-- Companies / Tenants -->
            @php
                $isCompaniesActive = request()->routeIs('superadmin.companies.*');
            @endphp
            <li class="side-nav-item {{ $isCompaniesActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarCompanies"
                    aria-expanded="{{ $isCompaniesActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isCompaniesActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-building-store"></i></span>
                    <span class="menu-text"> Companies / Tenants </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isCompaniesActive ? 'show' : '' }}" id="sidebarCompanies">
                    <ul class="sub-menu sub-menu-tree">
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.companies.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.companies.index') ? 'active' : '' }}">
                                <span class="menu-text">All Companies</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.companies.create') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.companies.create') ? 'active' : '' }}">
                                <span class="menu-text">Add Company</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Plans & Subscriptions -->
            @php
                $isPlansActive =
                    request()->routeIs('superadmin.plans.*') ||
                    request()->routeIs('superadmin.subscriptions.*') ||
                    request()->routeIs('superadmin.transactions.*');
            @endphp
            <li class="side-nav-item {{ $isPlansActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarPlans"
                    aria-expanded="{{ $isPlansActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isPlansActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-crown"></i></span>
                    <span class="menu-text"> Plans & Subscriptions </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isPlansActive ? 'show' : '' }}" id="sidebarPlans">
                    <ul class="sub-menu sub-menu-tree">
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.plans.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}">
                                <span class="menu-text">Pricing Plans</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.subscriptions.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}">
                                <span class="menu-text">Active Subscriptions</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.transactions.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.transactions.*') ? 'active' : '' }}">
                                <span class="menu-text">Payment Transactions</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-title mt-2">Platform Administration</li>

            <!-- Platform Users & Roles -->
            @php
                $isUsersActive = request()->routeIs('superadmin.users.*') || request()->routeIs('superadmin.roles.*');
            @endphp
            <li class="side-nav-item {{ $isUsersActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarUsers"
                    aria-expanded="{{ $isUsersActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isUsersActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-users"></i></span>
                    <span class="menu-text"> Platform Users & Roles </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isUsersActive ? 'show' : '' }}" id="sidebarUsers">
                    <ul class="sub-menu sub-menu-tree">
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.users.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                                <span class="menu-text">Admin Staff</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.roles.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.roles.*') ? 'active' : '' }}">
                                <span class="menu-text">Roles & Permissions</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Global Settings -->
            @php
                $isSettingsActive = request()->routeIs('superadmin.settings.*');
            @endphp
            <li class="side-nav-item {{ $isSettingsActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarSettings"
                    aria-expanded="{{ $isSettingsActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isSettingsActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-settings"></i></span>
                    <span class="menu-text"> Global Settings </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isSettingsActive ? 'show' : '' }}" id="sidebarSettings">
                    <ul class="sub-menu sub-menu-tree">
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.settings.general') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.settings.general') ? 'active' : '' }}">
                                <span class="menu-text">General Setup</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.settings.payment') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.settings.payment') ? 'active' : '' }}">
                                <span class="menu-text">Payment Gateways</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.settings.email') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.settings.email') ? 'active' : '' }}">
                                <span class="menu-text">Email & SMS</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- System & Security -->
            @php
                $isSystemActive = request()->routeIs('superadmin.system.*');
            @endphp
            <li class="side-nav-item {{ $isSystemActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarSystem"
                    aria-expanded="{{ $isSystemActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isSystemActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-server"></i></span>
                    <span class="menu-text"> System & Security </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isSystemActive ? 'show' : '' }}" id="sidebarSystem">
                    <ul class="sub-menu sub-menu-tree">
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.system.logs') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.system.logs') ? 'active' : '' }}">
                                <span class="menu-text">Activity Logs</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.system.backup') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.system.backup') ? 'active' : '' }}">
                                <span class="menu-text">Database Backup</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.system.info') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.system.info') ? 'active' : '' }}">
                                <span class="menu-text">System Info</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.system.update') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.system.update') ? 'active' : '' }}">
                                <span class="menu-text">Update Application</span>
                            </a>
                        </li>
                        <!-- Clear Cache — POST form for CSRF safety -->
                        <li class="side-nav-item">
                            <form method="POST" action="{{ route('superadmin.system.cache-clear') }}"
                                id="cacheClearForm">
                                @csrf
                                <button type="submit" class="side-nav-link w-100 text-start border-0 bg-transparent"
                                    onclick="return confirm('Clear all caches? (cache, config, view, route)')">
                                    <span class="menu-text">Clear Cache</span>
                                    <i class="ti ti-refresh ms-auto fs-14 text-muted"></i>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Global Master Data -->
            <li class="side-nav-title mt-2">Global Master Data</li>

            <!-- Business Types (Industry Setup) -->
            @php
                $isBusinessTypesActive =
                    request()->routeIs('superadmin.business-types.*') ||
                    request()->routeIs('superadmin.business-modules.*');
            @endphp
            <li class="side-nav-item {{ $isBusinessTypesActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarBusinessTypes"
                    aria-expanded="{{ $isBusinessTypesActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isBusinessTypesActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-briefcase"></i></span>
                    <span class="menu-text"> Business Types </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isBusinessTypesActive ? 'show' : '' }}" id="sidebarBusinessTypes">
                    <ul class="sub-menu sub-menu-tree">
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.business-types.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.business-types.*') ? 'active' : '' }}">
                                <span class="menu-text">Industry Types</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.business-modules.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.business-modules.*') ? 'active' : '' }}">
                                <span class="menu-text">Module Mapping</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Global Items & Inventory Setup -->
            @php
                $isGlobalItemsActive =
                    request()->routeIs('superadmin.global-categories.*') ||
                    request()->routeIs('superadmin.global-units.*') ||
                    request()->routeIs('superadmin.global-taxes.*') ||
                    request()->routeIs('superadmin.global-attributes.*');
            @endphp
            <li class="side-nav-item {{ $isGlobalItemsActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarGlobalItems"
                    aria-expanded="{{ $isGlobalItemsActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isGlobalItemsActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-package"></i></span>
                    <span class="menu-text"> Global Inventory Setup </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isGlobalItemsActive ? 'show' : '' }}" id="sidebarGlobalItems">
                    <ul class="sub-menu sub-menu-tree">
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.global-categories.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.global-categories.*') ? 'active' : '' }}">
                                <span class="menu-text">Categories</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.global-units.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.global-units.*') ? 'active' : '' }}">
                                <span class="menu-text">Units (UOM)</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.global-taxes.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.global-taxes.*') ? 'active' : '' }}">
                                <span class="menu-text">Taxes & VAT</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.global-attributes.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.global-attributes.*') ? 'active' : '' }}">
                                <span class="menu-text">Attributes (Color/Size)</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- POS & Customization -->
            <li class="side-nav-title mt-2">POS & Customization</li>

            <!-- Receipt & Print Settings -->
            @php
                $isPrintSettingsActive =
                    request()->routeIs('superadmin.invoice-templates.*') ||
                    request()->routeIs('superadmin.barcode-settings.*') ||
                    request()->routeIs('superadmin.email-templates.*');
            @endphp
            <li class="side-nav-item {{ $isPrintSettingsActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarPrintSettings"
                    aria-expanded="{{ $isPrintSettingsActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isPrintSettingsActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-receipt"></i></span>
                    <span class="menu-text"> Receipt & Print </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isPrintSettingsActive ? 'show' : '' }}" id="sidebarPrintSettings">
                    <ul class="sub-menu sub-menu-tree">
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.invoice-templates.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.invoice-templates.*') ? 'active' : '' }}">
                                <span class="menu-text">Invoice Templates</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.barcode-settings.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.barcode-settings.*') ? 'active' : '' }}">
                                <span class="menu-text">Barcode Settings</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.email-templates.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.email-templates.*') ? 'active' : '' }}">
                                <span class="menu-text">Email Templates</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Add-ons / Feature Toggles -->
            @php
                $isAddonsActive = request()->routeIs('superadmin.addons.*');
            @endphp
            <li class="side-nav-item {{ $isAddonsActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarAddons"
                    aria-expanded="{{ $isAddonsActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isAddonsActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-puzzle"></i></span>
                    <span class="menu-text"> Add-ons & Modules </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isAddonsActive ? 'show' : '' }}" id="sidebarAddons">
                    <ul class="sub-menu sub-menu-tree">
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.addons.index') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.addons.index') ? 'active' : '' }}">
                                <span class="menu-text">Installed Add-ons</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.addons.marketplace') }}"
                                class="side-nav-link {{ request()->routeIs('superadmin.addons.marketplace') ? 'active' : '' }}">
                                <span class="menu-text">Marketplace</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Helpdesk & Support -->
            <li class="side-nav-title mt-2">Helpdesk & Support</li>

            <li class="side-nav-item">
                <a href="{{ route('superadmin.support-tickets.index') }}"
                    class="side-nav-link {{ request()->routeIs('superadmin.support-tickets.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-lifebuoy"></i></span>
                    <span class="menu-text"> Support Tickets </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('superadmin.tenants.index') }}"
                    class="side-nav-link {{ request()->routeIs('superadmin.tenants.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-login"></i></span>
                    <span class="menu-text"> Login As Tenant </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('superadmin.announcements.index') }}"
                    class="side-nav-link {{ request()->routeIs('superadmin.announcements.*') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-megaphone"></i></span>
                    <span class="menu-text"> Announcements </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="https://docs.yourapp.com" target="_blank" rel="noopener noreferrer" class="side-nav-link">
                    <span class="menu-icon"><i class="ti ti-book-2"></i></span>
                    <span class="menu-text"> Documentation </span>
                    <span class="badge bg-info-lt text-info ms-auto"><i class="ti ti-external-link"
                            style="font-size:10px;"></i></span>
                </a>
            </li>

            <!-- Global Reports -->
            <li class="side-nav-title mt-2">Global Reports</li>

            @php
                $isReportsActive = request()->routeIs('superadmin.reports.*');
            @endphp
            <li class="side-nav-item {{ $isReportsActive ? 'menu-open' : '' }}">
                <a data-bs-toggle="collapse" href="#sidebarReports"
                    aria-expanded="{{ $isReportsActive ? 'true' : 'false' }}"
                    class="side-nav-link {{ $isReportsActive ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-chart-bar"></i></span>
                    <span class="menu-text"> Reports </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $isReportsActive ? 'show' : '' }}" id="sidebarReports">
                    <ul class="sub-menu sub-menu-tree">
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.reports.index', ['type' => 'revenue']) }}"
                                class="side-nav-link {{ $isReportsActive && request('type', 'revenue') === 'revenue' ? 'active' : '' }}">
                                <span class="menu-text">SaaS Revenue</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('superadmin.reports.index', ['type' => 'tenant-usage']) }}"
                                class="side-nav-link {{ $isReportsActive && request('type') === 'tenant-usage' ? 'active' : '' }}">
                                <span class="menu-text">Tenant Usage</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-title mt-2">Account</li>

            <!-- Profile -->
            <li class="side-nav-item">
                <a href="{{ route('profile.edit') }}"
                    class="side-nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-user"></i></span>
                    <span class="menu-text"> My Profile </span>
                </a>
            </li>

            <!-- Logout -->
            <li class="side-nav-item">
                <a href="{{ route('logout') }}" class="side-nav-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <span class="menu-icon"><i class="ti ti-logout"></i></span>
                    <span class="menu-text"> Logout </span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>

        </ul>

        <!-- Help Box -->
        <div class="help-box text-center">
            <h5 class="fw-semibold fs-16">Super Admin Panel</h5>
            <p class="mb-3 text-muted">Manage your SaaS platform, tenants, and global settings.</p>
            <a href="{{ route('superadmin.dashboard') }}" class="btn btn-primary btn-sm">Go to Dashboard</a>
        </div>

        <div class="clearfix"></div>
    </div>
</div>
