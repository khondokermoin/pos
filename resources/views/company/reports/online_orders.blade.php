@extends('layouts.admin_master')

@section('title', 'Online Orders Report')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="ti ti-world me-2 text-primary"></i>Online Orders Report
            </h4>
            <p class="text-muted mb-0 small">
                Track online orders, delivery locations, and branch routing performance.
            </p>
        </div>
        <a href="{{ route('company.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    {{-- ── Date Range Filter ──────────────────────────────────────────────────── --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('company.reports.online-orders') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">From Date</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">To Date</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="ti ti-filter me-1"></i> Apply Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Summary Stats ──────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
                        style="width:56px;height:56px;">
                        <i class="ti ti-shopping-cart text-primary fs-4"></i>
                    </div>
                    <h2 class="fw-bold mb-1">{{ number_format($totalOrders) }}</h2>
                    <p class="text-muted small mb-0">Total Online Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
                        style="width:56px;height:56px;">
                        <i class="ti ti-currency-taka text-success fs-4"></i>
                    </div>
                    <h2 class="fw-bold mb-1">৳{{ number_format($totalRevenue, 0) }}</h2>
                    <p class="text-muted small mb-0">Total Revenue</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
                        style="width:56px;height:56px;">
                        <i class="ti ti-clock text-warning fs-4"></i>
                    </div>
                    <h2 class="fw-bold mb-1">{{ number_format($pendingOrders) }}</h2>
                    <p class="text-muted small mb-0">Pending Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
                        style="width:56px;height:56px;">
                        <i class="ti ti-map-pin text-info fs-4"></i>
                    </div>
                    <h2 class="fw-bold mb-1">{{ number_format($uniqueCities) }}</h2>
                    <p class="text-muted small mb-0">Unique Delivery Cities</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">

        {{-- ── Orders by City ──────────────────────────────────────────────────── --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="ti ti-map me-2 text-primary"></i>Orders by Delivery Location
                    </h6>
                    <span class="badge bg-primary-subtle text-primary">{{ $ordersByCity->count() }} cities</span>
                </div>
                <div class="card-body p-0">
                    @if ($ordersByCity->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="ti ti-map-off fs-1 d-block mb-2 opacity-25"></i>
                            No location data for this period.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">City / District</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end pe-4">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordersByCity as $row)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-medium">{{ $row->delivery_city ?? '—' }}</div>
                                                @if ($row->delivery_district && $row->delivery_district !== $row->delivery_city)
                                                    <small class="text-muted">{{ $row->delivery_district }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                                    {{ number_format($row->order_count) }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4 fw-semibold">
                                                ৳{{ number_format($row->total_revenue, 0) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Orders by Branch ────────────────────────────────────────────────── --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="ti ti-building-store me-2 text-success"></i>Orders by Branch
                    </h6>
                    <span class="badge bg-success-subtle text-success">{{ $ordersByBranch->count() }} branches</span>
                </div>
                <div class="card-body p-0">
                    @if ($ordersByBranch->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="ti ti-building-off fs-1 d-block mb-2 opacity-25"></i>
                            No branch data for this period.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Branch</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-center">Cities</th>
                                        <th class="text-end pe-4">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordersByBranch as $row)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-medium">{{ $row->branch?->name ?? 'Unknown Branch' }}</div>
                                                @if ($row->branch?->city)
                                                    <small class="text-muted">
                                                        <i class="ti ti-map-pin me-1"></i>{{ $row->branch->city }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                                    {{ number_format($row->order_count) }}
                                                </span>
                                            </td>
                                            <td class="text-center text-muted small">
                                                {{ $row->cities_served }}
                                            </td>
                                            <td class="text-end pe-4 fw-semibold">
                                                ৳{{ number_format($row->total_revenue, 0) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Routing Method Breakdown ─────────────────────────────────────────────── --}}
    @if (!empty($routingBreakdown))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="ti ti-route me-2 text-info"></i>Branch Routing Method Breakdown
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @php
                        $routingLabels = [
                            'coverage_match' => [
                                'label' => 'Coverage Area Match',
                                'color' => 'success',
                                'icon' => 'ti-map-check',
                            ],
                            'distance' => ['label' => 'Nearest by GPS Distance', 'color' => 'info', 'icon' => 'ti-gps'],
                            'default' => ['label' => 'Default Branch', 'color' => 'warning', 'icon' => 'ti-star'],
                            'fallback' => [
                                'label' => 'Fallback (Any Branch)',
                                'color' => 'secondary',
                                'icon' => 'ti-refresh',
                            ],
                        ];
                        $totalRoutingOrders = array_sum($routingBreakdown);
                    @endphp
                    @foreach ($routingLabels as $method => $meta)
                        @php $count = $routingBreakdown[$method] ?? 0; @endphp
                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 text-center h-100">
                                <i class="ti {{ $meta['icon'] }} text-{{ $meta['color'] }} fs-3 d-block mb-2"></i>
                                <h4 class="fw-bold mb-1">{{ number_format($count) }}</h4>
                                <p class="text-muted small mb-2">{{ $meta['label'] }}</p>
                                @if ($totalRoutingOrders > 0)
                                    <div class="progress" style="height:4px;">
                                        <div class="progress-bar bg-{{ $meta['color'] }}"
                                            style="width: {{ round(($count / $totalRoutingOrders) * 100) }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ round(($count / $totalRoutingOrders) * 100) }}%</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 p-3 bg-light rounded-3">
                    <p class="text-muted small mb-0">
                        <i class="ti ti-info-circle me-1"></i>
                        <strong>Coverage Area Match</strong> = Customer's city matched a branch's configured coverage areas
                        (most accurate).
                        <strong>GPS Distance</strong> = Nearest branch calculated by Haversine formula.
                        <strong>Default</strong> = Routed to the branch flagged as default.
                        <strong>Fallback</strong> = First available branch (no location data provided).
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Recent Online Orders Table ───────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold">
                <i class="ti ti-list me-2 text-primary"></i>Recent Online Orders
            </h6>
            <span class="text-muted small">
                {{ $recentOrders->total() }} total orders
            </span>
        </div>
        <div class="card-body p-0">
            @if ($recentOrders->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="ti ti-shopping-cart-off fs-1 d-block mb-2 opacity-25"></i>
                    No online orders found for this period.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Invoice</th>
                                <th>Customer</th>
                                <th>Delivery Location</th>
                                <th>Routed Branch</th>
                                <th>Routing Method</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Status</th>
                                <th class="pe-4">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentOrders as $order)
                                <tr>
                                    <td class="ps-4">
                                        <code class="text-primary">{{ $order->invoice_no }}</code>
                                    </td>
                                    <td>
                                        <div class="fw-medium">
                                            {{ $order->customer_name ?? ($order->customer?->name ?? 'Guest') }}</div>
                                        @if ($order->customer_phone)
                                            <small class="text-muted">{{ $order->customer_phone }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $order->delivery_city ?? '—' }}</div>
                                        @if ($order->delivery_district && $order->delivery_district !== $order->delivery_city)
                                            <small class="text-muted">{{ $order->delivery_district }}</small>
                                        @endif
                                        @if ($order->delivery_area)
                                            <small class="text-muted d-block">{{ $order->delivery_area }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->routedBranch)
                                            <div class="fw-medium">{{ $order->routedBranch->name }}</div>
                                            @if ($order->routedBranch->city)
                                                <small class="text-muted">{{ $order->routedBranch->city }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $methodBadge = [
                                                'coverage_match' => ['bg-success-subtle text-success', 'Area Match'],
                                                'distance' => ['bg-info-subtle text-info', 'GPS Distance'],
                                                'default' => ['bg-warning-subtle text-warning', 'Default'],
                                                'fallback' => ['bg-secondary-subtle text-secondary', 'Fallback'],
                                            ];
                                            $badge = $methodBadge[$order->routing_method] ?? [
                                                'bg-light text-muted',
                                                $order->routing_method ?? '—',
                                            ];
                                        @endphp
                                        <span class="badge {{ $badge[0] }} px-2 py-1 rounded-pill small">
                                            {{ $badge[1] }}
                                        </span>
                                        @if ($order->routing_distance_km)
                                            <small
                                                class="text-muted d-block">{{ number_format($order->routing_distance_km, 1) }}
                                                km</small>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold">
                                        ৳{{ number_format($order->total_amount, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusClass = match ($order->status) {
                                                'completed' => 'bg-success-subtle text-success',
                                                'cancelled' => 'bg-danger-subtle text-danger',
                                                default => 'bg-warning-subtle text-warning',
                                            };
                                        @endphp
                                        <span
                                            class="badge {{ $statusClass }} px-2 py-1 rounded-pill small text-capitalize">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="pe-4">
                                        <small class="text-muted">{{ $order->created_at->format('d M Y') }}</small>
                                        <small
                                            class="text-muted d-block">{{ $order->created_at->format('h:i A') }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($recentOrders->hasPages())
                    <div class="px-4 py-3 border-top">
                        {{ $recentOrders->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

@endsection
