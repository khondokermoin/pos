@extends('layouts.admin_master')

@section('title', 'Product Details')

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0">
                <i class="ti ti-package me-2 text-primary"></i>{{ $product->name }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('company.products.index') }}">Products</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($product->name, 30) }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end d-flex justify-content-end gap-2">
            <a href="{{ route('company.products.edit', $product) }}" class="btn btn-warning">
                <i class="ti ti-pencil me-1"></i> Edit Product
            </a>
            <a href="{{ route('company.products.index') }}" class="btn btn-light">
                <i class="ti ti-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Left: Product Info Card --}}
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    {{-- Product Avatar --}}
                    <div class="mx-auto mb-3 rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                        style="width:80px;height:80px;">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                class="rounded-circle" style="width:80px;height:80px;object-fit:cover;">
                        @else
                            <span class="text-primary fw-bold" style="font-size:2rem;">
                                {{ strtoupper(substr($product->name, 0, 1)) }}
                            </span>
                        @endif
                    </div>

                    <h4 class="mb-1">{{ $product->name }}</h4>

                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }} fs-6">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if ($product->has_variants)
                            <span class="badge bg-info-subtle text-info fs-6">
                                <i class="ti ti-layers me-1"></i>Has Variants
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-body border-top pt-3">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Category</dt>
                        <dd class="col-7 small fw-semibold">
                            {{ optional($product->category)->name ?? '—' }}
                        </dd>

                        <dt class="col-5 text-muted small">Brand</dt>
                        <dd class="col-7 small fw-semibold">
                            {{ optional($product->brand)->name ?? '—' }}
                        </dd>

                        <dt class="col-5 text-muted small">Variants</dt>
                        <dd class="col-7 small fw-semibold">
                            {{ $product->variants->count() }} variant(s)
                        </dd>

                        <dt class="col-5 text-muted small">Total Stock</dt>
                        <dd class="col-7 small fw-semibold">
                            @php $totalStock = $product->variants->sum(fn($v) => optional($v->stock)->quantity ?? 0); @endphp
                            <span class="badge bg-{{ $totalStock > 0 ? 'success' : 'danger' }}">
                                {{ $totalStock }} pcs
                            </span>
                        </dd>

                        <dt class="col-5 text-muted small">Added On</dt>
                        <dd class="col-7 small fw-semibold">
                            {{ $product->created_at->format('d M Y') }}
                        </dd>
                    </dl>
                </div>

                @if ($product->description)
                    <div class="card-body border-top pt-3">
                        <h6 class="text-muted small text-uppercase fw-semibold mb-2">Description</h6>
                        <p class="text-muted small mb-0">{{ $product->description }}</p>
                    </div>
                @endif
            </div>

            {{-- Quick Actions --}}
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-semibold mb-3">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('company.products.edit', $product) }}" class="btn btn-warning btn-sm">
                            <i class="ti ti-pencil me-1"></i> Edit This Product
                        </a>
                        <a href="{{ route('company.inventory.low-stock') }}" class="btn btn-outline-danger btn-sm">
                            <i class="ti ti-alert-triangle me-1"></i> View Low Stock Alerts
                        </a>
                        <a href="{{ route('company.inventory.stock-adjust') }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-adjustments me-1"></i> Adjust Stock
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Variants & Stock Table --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ti ti-layers me-2"></i>Variants & Stock
                    </h5>
                    <span class="badge bg-primary-subtle text-primary">
                        {{ $product->variants->count() }} variant(s)
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">SKU</th>
                                    <th>Attributes</th>
                                    <th class="text-end">Cost</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-center">Reorder</th>
                                    <th class="text-center pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->variants as $variant)
                                    @php
                                        $qty = optional($variant->stock)->quantity ?? 0;
                                        $reorder = $variant->reorder_level ?? 5;
                                        $stockStatus =
                                            $qty === 0 ? 'danger' : ($qty <= $reorder ? 'warning' : 'success');
                                        $stockLabel =
                                            $qty === 0 ? 'Out of Stock' : ($qty <= $reorder ? 'Low Stock' : 'In Stock');
                                    @endphp
                                    <tr
                                        class="{{ $qty === 0 ? 'table-danger' : ($qty <= $reorder ? 'table-warning' : '') }} bg-opacity-25">
                                        <td class="ps-3">
                                            <code class="bg-light px-2 py-1 rounded small">{{ $variant->sku }}</code>
                                            @if ($variant->barcode)
                                                <br><small class="text-muted">{{ $variant->barcode }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($variant->attributes)
                                                @php
                                                    $attrs = is_string($variant->attributes)
                                                        ? json_decode($variant->attributes, true)
                                                        : $variant->attributes;
                                                @endphp
                                                @if (is_array($attrs) && count($attrs))
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach ($attrs as $attr)
                                                            <span class="badge bg-light text-dark border">
                                                                {{ $attr['key'] ?? '' }}: {{ $attr['value'] ?? '' }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted small">Default</span>
                                                @endif
                                            @else
                                                <span class="text-muted small">Default</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <small class="text-muted">৳{{ number_format($variant->cost_price, 2) }}</small>
                                        </td>
                                        <td class="text-end">
                                            <strong>৳{{ number_format($variant->selling_price, 2) }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-{{ $stockStatus }} fs-6 px-3">{{ $qty }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted small">{{ $reorder }}</span>
                                        </td>
                                        <td class="text-center pe-3">
                                            <span class="badge bg-{{ $stockStatus }}-subtle text-{{ $stockStatus }}">
                                                {{ $stockLabel }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            No variants found for this product.
                                        </td>
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
