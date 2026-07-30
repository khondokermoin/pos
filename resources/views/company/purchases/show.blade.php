@extends('layouts.admin_master')

@section('title', 'Purchase Details')

@push('styles')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .card {
                border: 1px solid #dee2e6 !important;
            }
        }
    </style>
@endpush

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3 align-items-center no-print">
        <div class="col-sm-6">
            <h4 class="page-title mb-0">
                <i class="ti ti-receipt me-2 text-primary"></i>Purchase Details
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('company.purchases.index') }}">Purchases</a></li>
                    <li class="breadcrumb-item active">Purchase #{{ $purchase->id }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end d-flex justify-content-end gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="ti ti-printer me-1"></i> Print
            </button>
            <a href="{{ route('company.purchases.index') }}" class="btn btn-light">
                <i class="ti ti-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    {{-- Purchase Info Card --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="ti ti-file-invoice me-2"></i>Purchase #{{ $purchase->id }}
            </h5>
            @php
                $statusColor = match ($purchase->status ?? 'completed') {
                    'completed' => 'success',
                    'pending' => 'warning',
                    'cancelled' => 'danger',
                    default => 'secondary',
                };
            @endphp
            <span class="badge bg-{{ $statusColor }} fs-6">
                {{ ucfirst($purchase->status ?? 'completed') }}
            </span>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Left Column: Supplier Info --}}
                <div class="col-md-6 mb-3 mb-md-0">
                    <h6 class="text-muted text-uppercase fw-semibold mb-3" style="font-size:0.75rem;letter-spacing:0.05em;">
                        <i class="ti ti-truck me-1"></i> Supplier Information
                    </h6>
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle bg-warning bg-opacity-20 d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:48px;height:48px;">
                            <span class="text-warning fw-bold fs-5">
                                {{ strtoupper(substr(optional($purchase->supplier)->name ?? 'S', 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-1">{{ optional($purchase->supplier)->name ?? '—' }}</h5>
                            @if (optional($purchase->supplier)->phone)
                                <p class="mb-1 text-muted small">
                                    <i class="ti ti-phone me-1"></i>{{ $purchase->supplier->phone }}
                                </p>
                            @endif
                            @if (optional($purchase->supplier)->email)
                                <p class="mb-1 text-muted small">
                                    <i class="ti ti-mail me-1"></i>{{ $purchase->supplier->email }}
                                </p>
                            @endif
                            @if (optional($purchase->supplier)->address)
                                <p class="mb-0 text-muted small">
                                    <i class="ti ti-map-pin me-1"></i>{{ $purchase->supplier->address }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="row g-2">
                        <div class="col-6">
                            <small class="text-muted d-block">Purchase Date</small>
                            <strong>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Recorded On</small>
                            <strong>{{ $purchase->created_at->format('d M Y, h:i A') }}</strong>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Branch & Summary --}}
                <div class="col-md-6">
                    <h6 class="text-muted text-uppercase fw-semibold mb-3" style="font-size:0.75rem;letter-spacing:0.05em;">
                        <i class="ti ti-building-store me-1"></i> Delivery & Summary
                    </h6>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Delivered To</small>
                        @if ($purchase->branch)
                            <span class="badge bg-info-subtle text-info fs-6 px-3 py-2">
                                <i class="ti ti-building-store me-1"></i>{{ $purchase->branch->name }}
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary fs-6 px-3 py-2">
                                <i class="ti ti-home me-1"></i>Central Warehouse / Head Office
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Recorded By</small>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                style="width:32px;height:32px;">
                                <span class="text-primary fw-bold small">
                                    {{ strtoupper(substr(optional($purchase->user)->name ?? 'U', 0, 1)) }}
                                </span>
                            </div>
                            <span class="fw-semibold">{{ optional($purchase->user)->name ?? '—' }}</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Items</span>
                            <strong>{{ $purchase->items->count() }} item(s)</strong>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold fs-5">Grand Total</span>
                            <span class="fw-bold fs-4 text-success">৳{{ number_format($purchase->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Purchase Items Table --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="ti ti-list me-2"></i>Purchase Items</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Product</th>
                            <th>SKU / Variant</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end pe-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchase->items as $index => $item)
                            <tr>
                                <td class="ps-3">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                            style="width:36px;height:36px;min-width:36px;">
                                            <span class="text-primary fw-bold small">
                                                {{ strtoupper(substr(optional(optional($item->variant)->product)->name ?? 'P', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">
                                                {{ optional(optional($item->variant)->product)->name ?? '—' }}
                                            </div>
                                            @if (optional(optional($item->variant)->product)->category)
                                                <small class="text-muted">
                                                    {{ $item->variant->product->category->name ?? '' }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">
                                        {{ optional($item->variant)->sku ?? '—' }}
                                    </code>
                                    @if (optional($item->variant)->name && optional($item->variant)->name !== 'Default')
                                        <br><small class="text-muted">{{ $item->variant->name }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fs-6 px-3">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="text-end">৳{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end pe-3">
                                    <strong>৳{{ number_format($item->subtotal ?? $item->quantity * $item->unit_price, 2) }}</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No items found for this purchase.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-bold pe-3 py-3">
                                <span class="fs-5">Grand Total:</span>
                            </td>
                            <td class="text-end pe-3 py-3">
                                <span class="fw-bold fs-4 text-success">
                                    ৳{{ number_format($purchase->total_amount, 2) }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Bottom Action Bar --}}
    <div class="mt-3 d-flex justify-content-between no-print">
        <a href="{{ route('company.purchases.index') }}" class="btn btn-light">
            <i class="ti ti-arrow-left me-1"></i> Back to Purchases
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="ti ti-printer me-1"></i> Print This Purchase
        </button>
    </div>
@endsection
