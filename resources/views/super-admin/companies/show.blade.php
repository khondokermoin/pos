@extends('layouts.admin_master')

@section('title', 'Company Details')

@php
    // ✅ Same logo URL fix used in edit.blade.php: asset($company->logo) breaks when
    // the file was saved via Storage::disk('public')->put(...), since that path is
    // served from /storage/... (via the storage:link symlink), not from public/ root.
    $logoUrl = null;
    if (!empty($company->logo)) {
        if (\Illuminate\Support\Str::startsWith($company->logo, ['http://', 'https://'])) {
            $logoUrl = $company->logo;
        } elseif (\Illuminate\Support\Str::startsWith($company->logo, 'storage/')) {
            $logoUrl = asset($company->logo);
        } else {
            $logoUrl = asset('storage/' . ltrim($company->logo, '/'));
        }
    }
    $avatarFallback =
        'https://ui-avatars.com/api/?name=' . urlencode($company->name) . '&background=random&color=fff&size=128';

    $daysLeft = null;
    if (!empty($company->trial_ends_at)) {
        $daysLeft = (int) floor(now()->diffInDays($company->trial_ends_at, false));
    }
@endphp

@section('content')
    <!-- Page Title & Breadcrumb -->
    <div class="row mb-2">
        <div class="col-sm-6">
            <h4 class="page-title">Company Details</h4>
        </div>
        <div class="col-sm-6 text-sm-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-end mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.companies.index') }}">Companies</a></li>
                    <li class="breadcrumb-item active">{{ $company->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Company Info Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card text-center">
                <div class="card-body">
                    <img src="{{ $logoUrl ?? $avatarFallback }}" class="rounded-circle img-thumbnail mb-3" width="128"
                        height="128" style="object-fit: cover;" alt="{{ $company->name }}"
                        onerror="this.onerror=null; this.src='{{ $avatarFallback }}';">
                    <h4 class="mb-1">{{ $company->name }}</h4>
                    <p class="text-muted mb-2">{{ $company->email }}</p>

                    {{-- Status badge --}}
                    @if ($company->status == 'active')
                        <span class="badge bg-success-subtle text-success mb-2"><i class="ti ti-check"></i> Active</span>
                    @elseif ($company->status == 'trial')
                        <span class="badge bg-warning-subtle text-warning mb-2"><i class="ti ti-clock"></i> Trial</span>
                    @elseif ($company->status == 'suspended')
                        <span class="badge bg-danger-subtle text-danger mb-2"><i class="ti ti-alert-triangle"></i>
                            Suspended</span>
                    @else
                        <span
                            class="badge bg-secondary-subtle text-secondary mb-2">{{ ucfirst($company->status ?? 'Inactive') }}</span>
                    @endif

                    <div class="d-flex justify-content-center flex-wrap gap-2 mb-2">
                        <a href="{{ route('superadmin.companies.edit', $company->id) }}" class="btn btn-warning btn-sm">
                            <i class="ti ti-edit me-1"></i> Edit
                        </a>

                        {{-- ✅ Fixed: this previously linked to a generic tenants index page
                             regardless of which company you were viewing. It now impersonates
                             *this specific* company's admin, matching the same action used on
                             the companies index page - and only shows if there's actually an
                             owner/admin to log in as. --}}
                        @if ($company->owner ?? $company->user_id)
                            <form action="{{ route('superadmin.companies.impersonate', $company->id) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="ti ti-login me-1"></i> Login as Tenant
                                </button>
                            </form>
                        @endif
                    </div>

                    <hr>

                    <div class="row text-start mt-3">
                        @if ($company->custom_domain)
                            <div class="col-12 mb-2">
                                <p class="text-muted mb-1 fs-13"><i class="ti ti-world-www me-1"></i> Custom Domain:</p>
                                <p class="fw-semibold mb-0">{{ $company->custom_domain }}</p>
                            </div>
                        @endif

                        @if (!empty($company->theme_settings['primary_color']))
                            <div class="col-12 mb-2">
                                <p class="text-muted mb-1 fs-13"><i class="ti ti-palette me-1"></i> Primary Color:</p>
                                <div class="fw-semibold d-flex align-items-center gap-2">
                                    <span class="border rounded"
                                        style="width: 18px; height: 18px; background-color: {{ $company->theme_settings['primary_color'] }};"></span>
                                    <span>{{ $company->theme_settings['primary_color'] }}</span>
                                </div>
                            </div>
                        @endif
                        @if ($company->contact_person)
                            <div class="col-12 mb-2">
                                <p class="text-muted mb-1 fs-13"><i class="ti ti-user me-1"></i> Contact Person:</p>
                                <p class="fw-semibold">{{ $company->contact_person }}</p>
                            </div>
                        @endif

                        <div class="col-12 mb-2">
                            <p class="text-muted mb-1 fs-13"><i class="ti ti-phone me-1"></i> Phone:</p>
                            <p class="fw-semibold">{{ $company->phone ?: 'N/A' }}</p>
                        </div>

                        <div class="col-12 mb-2">
                            <p class="text-muted mb-1 fs-13"><i class="ti ti-map-pin me-1"></i> Address:</p>
                            <p class="fw-semibold mb-0">
                                @if ($company->address || $company->city || $company->country || $company->zip_code)
                                    {{ collect([$company->address, $company->city, $company->country, $company->zip_code])->filter()->implode(', ') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>

                        @if ($company->website)
                            <div class="col-12 mb-2">
                                <p class="text-muted mb-1 fs-13"><i class="ti ti-world me-1"></i> Website:</p>
                                <p class="fw-semibold mb-0">
                                    <a href="{{ $company->website }}" target="_blank"
                                        rel="noopener">{{ $company->website }}</a>
                                </p>
                            </div>
                        @endif

                        @if ($company->subdomain)
                            <div class="col-12 mb-2">
                                <p class="text-muted mb-1 fs-13"><i class="ti ti-link me-1"></i> Subdomain:</p>
                                <p class="fw-semibold mb-0">{{ $company->subdomain }}.yourdomain.com</p>
                            </div>
                        @endif

                        <div class="col-12 mb-2">
                            <p class="text-muted mb-1 fs-13"><i class="ti ti-calendar-event me-1"></i> Joined On:</p>
                            <p class="fw-semibold mb-0">{{ $company->created_at?->format('d M Y') ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription & Stats -->
        <div class="col-xl-8 col-lg-7">
            <!-- Subscription Info -->
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Subscription Details</h4>
                    <div class="row">
                        <div class="col-sm-4 mb-2 mb-sm-0">
                            <div class="bg-light p-3 rounded text-center">
                                <p class="text-muted mb-1 fs-13">Current Plan</p>
                                <h4 class="text-primary mb-0">{{ $company->plan->name ?? 'No Plan' }}</h4>
                                @if ($company->plan)
                                    <small class="text-muted">
                                        {{ $company->currency ?? '৳' }}{{ number_format($company->plan->price, 2) }}/month
                                    </small>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-4 mb-2 mb-sm-0">
                            <div class="bg-light p-3 rounded text-center">
                                <p class="text-muted mb-1 fs-13">Status</p>
                                @if ($company->status == 'active')
                                    <h4 class="text-success mb-0">Active</h4>
                                @elseif ($company->status == 'trial')
                                    <h4 class="text-warning mb-0">Trial</h4>
                                @elseif ($company->status == 'suspended')
                                    <h4 class="text-danger mb-0">Suspended</h4>
                                @else
                                    <h4 class="text-secondary mb-0">{{ ucfirst($company->status ?? 'Inactive') }}</h4>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded text-center">
                                <p class="text-muted mb-1 fs-13">
                                    {{ $company->trial_ends_at ? 'Trial Ends' : 'Expires On' }}
                                </p>
                                @if ($company->trial_ends_at)
                                    <h4
                                        class="{{ $daysLeft > 7 ? 'text-success' : ($daysLeft > 0 ? 'text-warning' : 'text-danger') }} mb-0">
                                        {{ \Carbon\Carbon::parse($company->trial_ends_at)->format('d M Y') }}
                                    </h4>
                                    <small class="text-muted">
                                        {{ $daysLeft > 0 ? $daysLeft . ' days left' : 'Expired' }}
                                    </small>
                                @else
                                    <h4 class="text-muted mb-0">N/A</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Stats -->
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Platform Usage</h4>
                    <div class="row">
                        <div class="col-sm-6 col-md-3 mb-3">
                            <div class="card bg-primary text-white shadow-none">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0 text-white">
                                                {{ $company->users_count ?? 0 }}{{ isset($company->plan->user_limit) ? ' / ' . $company->plan->user_limit : '' }}
                                            </h4>
                                            <p class="mb-0 mt-1 fs-13">Total Users</p>
                                        </div>
                                        <div class="avatar-md">
                                            <span class="avatar-title bg-transparent text-white rounded-circle">
                                                <i class="ti ti-users fs-2"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 mb-3">
                            <div class="card bg-success text-white shadow-none">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0 text-white">
                                                {{ $company->branches_count ?? 0 }}{{ isset($company->plan->branch_limit) ? ' / ' . $company->plan->branch_limit : '' }}
                                            </h4>
                                            <p class="mb-0 mt-1 fs-13">Branches</p>
                                        </div>
                                        <div class="avatar-md">
                                            <span class="avatar-title bg-transparent text-white rounded-circle">
                                                <i class="ti ti-building-store fs-2"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- ℹ️ Products/Categories/Total Sales were hardcoded dummy numbers
                             (1,240 / 89 / 5,432) in the old version with no real data behind
                             them. This app's Company model doesn't expose those counts yet
                             (only users_count / branches_count are used elsewhere, e.g. on
                             the companies index page), so rather than keep faking numbers,
                             these two cards only render if the controller actually passes
                             $stats['products'] / $stats['categories'] / $stats['sales'].
                             Add those counts (e.g. via withCount()) in the controller and
                             pass them as $stats to light these back up. --}}
                        @isset($stats['products'])
                            <div class="col-sm-6 col-md-3 mb-3">
                                <div class="card bg-warning text-white shadow-none">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0 text-white">{{ number_format($stats['products']) }}</h4>
                                                <p class="mb-0 mt-1 fs-13">Products</p>
                                            </div>
                                            <div class="avatar-md">
                                                <span class="avatar-title bg-transparent text-white rounded-circle">
                                                    <i class="ti ti-package fs-2"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endisset
                        @isset($stats['categories'])
                            <div class="col-sm-6 col-md-3 mb-3">
                                <div class="card bg-info text-white shadow-none">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0 text-white">{{ number_format($stats['categories']) }}</h4>
                                                <p class="mb-0 mt-1 fs-13">Categories</p>
                                            </div>
                                            <div class="avatar-md">
                                                <span class="avatar-title bg-transparent text-white rounded-circle">
                                                    <i class="ti ti-category fs-2"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endisset
                        @isset($stats['sales'])
                            <div class="col-sm-6 col-md-3 mb-3">
                                <div class="card bg-danger text-white shadow-none">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0 text-white">{{ number_format($stats['sales']) }}</h4>
                                                <p class="mb-0 mt-1 fs-13">Total Sales</p>
                                            </div>
                                            <div class="avatar-md">
                                                <span class="avatar-title bg-transparent text-white rounded-circle">
                                                    <i class="ti ti-chart-bar fs-2"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
