@extends('layouts.admin_master')

@section('content')
    <div class="container-fluid">

        {{-- ============================================================
         1-CLICK IMPERSONATION BANNER
         Shown ONLY when a Company Admin has logged in as this branch.
         The session key 'branch_impersonator_id' is set by BranchController@impersonate.
    ============================================================ --}}
        @if (session()->has('branch_impersonator_id'))
            <div class="alert alert-warning alert-dismissible d-flex align-items-center justify-content-between mb-3 border-0 shadow-sm"
                role="alert" style="border-left: 4px solid #f59e0b !important;">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-eye fs-4 text-warning"></i>
                    <div>
                        <strong>Impersonation Mode Active</strong>
                        <span class="ms-2 text-muted small">
                            You are viewing this branch as its Manager.
                            Logged in as: <strong>{{ auth()->user()->name }}</strong>
                        </span>
                    </div>
                </div>
                <a href="{{ route('branch.impersonate.leave') }}"
                    class="btn btn-sm btn-warning d-flex align-items-center gap-1 text-nowrap ms-3">
                    <i class="ti ti-arrow-back-up"></i>
                    Back to Company
                </a>
            </div>
        @endif

        {{-- Also show the banner if the session key was stored before login swap --}}
        @if (!session()->has('branch_impersonator_id') && session()->has('branch_impersonator_name'))
            <div class="alert alert-info d-flex align-items-center justify-content-between mb-3 border-0 shadow-sm"
                role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-eye fs-4 text-info"></i>
                    <span>
                        <strong>Impersonation Mode</strong> — You were logged in by
                        <strong>{{ session('branch_impersonator_name') }}</strong>.
                    </span>
                </div>
                <a href="{{ route('branch.impersonate.leave') }}"
                    class="btn btn-sm btn-info d-flex align-items-center gap-1 text-nowrap ms-3">
                    <i class="ti ti-arrow-back-up"></i>
                    Back to Company
                </a>
            </div>
        @endif

        <div class="page-title-box d-flex justify-content-between align-items-center">
            <h4 class="page-title mb-0">Branch Dashboard</h4>
            {{-- Quick-return button always visible for impersonators --}}
            @if (session()->has('branch_impersonator_id') || session()->has('branch_impersonator_name'))
                <a href="{{ route('branch.impersonate.leave') }}" class="btn btn-outline-warning btn-sm">
                    <i class="ti ti-arrow-back-up me-1"></i> Back to Company Panel
                </a>
            @endif
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Today's Sales</h6>
                            <h2 class="mb-0">{{ $todaySales }}</h2>
                        </div>
                        <i class="ti ti-receipt fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Today's Revenue</h6>
                            <h2 class="mb-0">৳{{ number_format($todayRevenue, 2) }}</h2>
                        </div>
                        <i class="ti ti-currency-taka fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Low Stock Items</h6>
                            <h2 class="mb-0">{{ $lowStockCount }}</h2>
                        </div>
                        <i class="ti ti-alert-triangle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Products</h6>
                            <h2 class="mb-0">{{ $totalProducts }}</h2>
                        </div>
                        <i class="ti ti-package fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Sales</h5>
                        <a href="{{ route('branch.sales.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales as $sale)
                                    <tr>
                                        <td><code>{{ $sale->invoice_no ?? '#' . $sale->id }}</code></td>
                                        <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                        <td>{{ $sale->items_count ?? 0 }}</td>
                                        <td><strong>৳{{ number_format($sale->total_amount ?? 0, 2) }}</strong></td>
                                        <td>{{ $sale->created_at->format('d M, h:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">No sales today.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
