@extends('layouts.admin_master')

@section('content')
    @if (session('subscription_warning'))
        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="ti ti-alert-triangle fs-4 me-3"></i>
            <div>
                <strong>Subscription Alert!</strong> {{ session('subscription_warning') }}
                <a href="{{ route('company.subscription.index') }}" class="alert-link ms-2">View Plans →</a>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $company = Auth::user()->company;
        $subscription = $company?->subscription;
        $daysLeft = $subscription?->daysRemaining() ?? 0;
    @endphp
    @if ($subscription && $subscription->status === 'active' && $daysLeft > 0 && $daysLeft <= 7)
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="ti ti-clock fs-4 me-3"></i>
            <div>
                <strong>⚠️ Subscription Expiring Soon!</strong>
                Your <strong>{{ $subscription->plan->name }}</strong> plan expires in
                <strong>{{ $daysLeft }} day(s)</strong> on {{ $subscription->ends_at->format('d M Y') }}.
                <a href="{{ route('company.subscription.index') }}" class="alert-link ms-2">Renew Now →</a>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mt-3">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="text-white-50">Today Sales</h6>
                    <h2>{{ $todaySales }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="text-white-50">Today Revenue</h6>
                    <h3>৳{{ number_format($todayRevenue, 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h6 class="text-white-50">Month Revenue</h6>
                    <h3>৳{{ number_format($monthRevenue, 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h6 class="text-white-50">Products</h6>
                    <h2>{{ $totalProducts }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body text-center">
                    <h6 class="text-white-50">Customers</h6>
                    <h2>{{ $totalCustomers }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h6 class="text-white-50">Low Stock</h6>
                    <h2>{{ $lowStockCount }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Sales</h5>
            <a href="{{ route('company.sales.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSales as $sale)
                        <tr>
                            <td><code>{{ $sale->invoice_no ?? '#' . $sale->id }}</code></td>
                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td><strong>৳{{ number_format($sale->total_amount ?? 0, 2) }}</strong></td>
                            <td>{{ $sale->created_at->format('d M, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">No sales yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
