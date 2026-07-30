@extends('layouts.admin_master')
@section('title', "Today's Report")
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-calendar-stats me-2 text-primary"></i>Today's Report</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('branch.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Today's Report</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <span class="badge bg-primary fs-6">{{ \Carbon\Carbon::parse($today)->format('d F Y') }}</span>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Revenue</h6>
                        <h3 class="mb-0">৳{{ number_format($totalRevenue, 2) }}</h3>
                    </div>
                    <i class="ti ti-currency-taka fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Orders</h6>
                        <h3 class="mb-0">{{ $totalOrders }}</h3>
                    </div>
                    <i class="ti ti-shopping-cart fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Discount</h6>
                        <h3 class="mb-0">৳{{ number_format($totalDiscount, 2) }}</h3>
                    </div>
                    <i class="ti ti-discount fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Today's Expenses</h6>
                        <h3 class="mb-0">৳{{ number_format($todayExpenses, 2) }}</h3>
                    </div>
                    <i class="ti ti-receipt-2 fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        {{-- Payment Method Breakdown --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="ti ti-credit-card me-2"></i>Payment Methods</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-success-subtle rounded">
                        <span><i class="ti ti-cash me-2 text-success"></i>Cash</span>
                        <strong class="text-success">৳{{ number_format($cashSales, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-primary-subtle rounded">
                        <span><i class="ti ti-credit-card me-2 text-primary"></i>Card</span>
                        <strong class="text-primary">৳{{ number_format($cardSales, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-2 bg-info-subtle rounded">
                        <span><i class="ti ti-device-mobile me-2 text-info"></i>Mobile Banking</span>
                        <strong class="text-info">৳{{ number_format($mobileSales, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Selling Items --}}
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="ti ti-trending-up me-2"></i>Top Selling Items Today</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Product</th>
                                    <th class="text-center">Qty Sold</th>
                                    <th class="text-end pe-3">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topItems as $i => $item)
                                    <tr>
                                        <td class="ps-3">{{ $i + 1 }}</td>
                                        <td>{{ optional($item->variant->product)->name ?? '—' }}</td>
                                        <td class="text-center"><span
                                                class="badge bg-primary-subtle text-primary">{{ $item->total_qty }}</span>
                                        </td>
                                        <td class="text-end pe-3">৳{{ number_format($item->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">No sales today</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hourly Sales --}}
    @if ($hourlySales->count() > 0)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="ti ti-clock me-2"></i>Hourly Sales Breakdown</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Hour</th>
                                <th class="text-center">Orders</th>
                                <th class="text-end pe-3">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hourlySales as $h)
                                <tr>
                                    <td class="ps-3">
                                        {{ str_pad($h->hour, 2, '0', STR_PAD_LEFT) }}:00 —
                                        {{ str_pad($h->hour + 1, 2, '0', STR_PAD_LEFT) }}:00
                                    </td>
                                    <td class="text-center">{{ $h->count }}</td>
                                    <td class="text-end pe-3">৳{{ number_format($h->revenue, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection
