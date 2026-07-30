@extends('layouts.admin_master')

@section('title', 'Low Stock Alerts')

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0">
                <i class="ti ti-alert-triangle me-2 text-danger"></i>Low Stock Alerts
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Low Stock Alerts</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('company.inventory.stock-adjust') }}" class="btn btn-primary">
                <i class="ti ti-adjustments me-1"></i> Adjust Stock
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Out of Stock</h6>
                        <h2 class="mb-0">{{ $outOfStockCount }}</h2>
                        <small class="text-white-50">Items with 0 quantity</small>
                    </div>
                    <i class="ti ti-package-off fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Critical / Low Stock</h6>
                        <h2 class="mb-0">{{ $criticalCount }}</h2>
                        <small class="text-white-50">Items at or below reorder level</small>
                    </div>
                    <i class="ti ti-alert-triangle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Low Stock Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="ti ti-list me-2"></i>Items Requiring Attention
            </h5>
            <span class="badge bg-danger fs-6">{{ $lowStockItems->total() }} items</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Branch</th>
                            <th class="text-center">Current Qty</th>
                            <th class="text-center">Reorder Level</th>
                            <th class="text-center">Shortage</th>
                            <th class="text-center pe-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockItems as $stock)
                            @php
                                $qty = $stock->quantity;
                                $reorder = $stock->reorder_level ?? 5;
                                $shortage = max(0, $reorder - $qty);
                                $isOutOfStock = $qty === 0;
                            @endphp
                            <tr class="{{ $isOutOfStock ? 'table-danger' : 'table-warning' }} bg-opacity-25">
                                <td class="ps-3">{{ $lowStockItems->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ $isOutOfStock ? 'bg-danger' : 'bg-warning' }} bg-opacity-20"
                                            style="width:36px;height:36px;min-width:36px;">
                                            <span
                                                class="{{ $isOutOfStock ? 'text-danger' : 'text-warning' }} fw-bold small">
                                                {{ strtoupper(substr(optional(optional($stock->variant)->product)->name ?? 'P', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">
                                                {{ optional(optional($stock->variant)->product)->name ?? '—' }}
                                            </div>
                                            @if (optional($stock->variant)->name && optional($stock->variant)->name !== 'Default')
                                                <small class="text-muted">{{ $stock->variant->name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded small">
                                        {{ optional($stock->variant)->sku ?? '—' }}
                                    </code>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ optional(optional(optional($stock->variant)->product)->category)->name ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($stock->branch)
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="ti ti-building-store me-1"></i>{{ $stock->branch->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="ti ti-home me-1"></i>Central
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge fs-6 px-3 bg-{{ $isOutOfStock ? 'danger' : 'warning' }}">
                                        {{ $qty }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="text-muted">{{ $reorder }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($shortage > 0)
                                        <span class="badge bg-danger-subtle text-danger">
                                            -{{ $shortage }} needed
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center pe-3">
                                    @if ($isOutOfStock)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @else
                                        <span class="badge bg-warning">Low Stock</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="ti ti-circle-check d-block mb-3 text-success" style="font-size:3rem;"></i>
                                    <h5 class="fw-semibold text-success">All Stock Levels are Healthy!</h5>
                                    <p class="small mb-0">No items are currently below their reorder level.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($lowStockItems->hasPages())
                <div class="px-3 py-2 d-flex justify-content-between align-items-center border-top">
                    <div class="text-muted small">
                        Showing {{ $lowStockItems->firstItem() }} to {{ $lowStockItems->lastItem() }}
                        of {{ $lowStockItems->total() }} items
                    </div>
                    {{ $lowStockItems->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
