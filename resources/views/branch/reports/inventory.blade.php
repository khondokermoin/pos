@extends('layouts.admin_master')
@section('title', 'Inventory Report')
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-clipboard-list me-2 text-primary"></i>Inventory Report</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('branch.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Inventory Report</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total SKUs</h6>
                        <h3 class="mb-0">{{ $totalItems }}</h3>
                    </div>
                    <i class="ti ti-package fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Out of Stock</h6>
                        <h3 class="mb-0">{{ $outOfStock }}</h3>
                    </div>
                    <i class="ti ti-package-off fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Low Stock (≤5)</h6>
                        <h3 class="mb-0">{{ $lowStock }}</h3>
                    </div>
                    <i class="ti ti-alert-triangle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Stock Value</h6>
                        <h3 class="mb-0">৳{{ number_format($totalStockVal, 0) }}</h3>
                    </div>
                    <i class="ti ti-currency-taka fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search product..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Stock Status</option>
                        <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of
                            Stock</option>
                        <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock
                        </option>
                        <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="ti ti-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('branch.reports.inventory') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="ti ti-x"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                            @php
                                $qty = $stock->quantity;
                                $statusColor = $qty == 0 ? 'danger' : ($qty <= 5 ? 'warning' : 'success');
                                $statusLabel = $qty == 0 ? 'Out of Stock' : ($qty <= 5 ? 'Low Stock' : 'In Stock');
                            @endphp
                            <tr>
                                <td class="ps-3">{{ $stocks->firstItem() + $loop->index }}</td>
                                <td>
                                    <strong>{{ optional($stock->variant->product)->name ?? '—' }}</strong>
                                    @if ($stock->variant->name ?? false)
                                        <small class="text-muted d-block">{{ $stock->variant->name }}</small>
                                    @endif
                                </td>
                                <td>{{ optional(optional($stock->variant->product)->category)->name ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} fs-6">
                                        {{ $qty }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="ti ti-package-off d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                                    <h5>No Stock Data</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($stocks->hasPages())
                <div class="px-3 py-2 border-top">
                    {{ $stocks->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
