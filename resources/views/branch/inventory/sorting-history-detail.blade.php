@extends('layouts.admin_master')

@section('title', 'Sorting Details')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Sorting Details</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('branch.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('branch.inventory.sorting-history') }}">Sorting History</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Details #{{ $history->id }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('branch.inventory.sorting-history') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to History
        </a>
    </div>

    <div class="row g-4">
        <!-- Summary Card (Left Side) -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title mb-0"><i class="ti ti-info-circle me-2 text-primary"></i>Summary</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Reference No:</span>
                            <span class="fw-semibold">{{ $history->reference_number ?: 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Sorted Date:</span>
                            <span class="fw-semibold">{{ $history->sorted_at->format('d M, Y h:i A') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Sorted By:</span>
                            <span class="fw-semibold">{{ $history->user->name ?? 'Unknown' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 border-top pt-3 mt-2">
                            <span class="text-muted fw-bold">Bulk Item:</span>
                        </li>
                        <li class="list-group-item px-0 pt-0">
                            <div class="bg-light p-3 rounded">
                                <div class="fw-bold text-primary mb-1">{{ $history->bulk_product_name }}</div>
                                <div class="text-muted">Total Quantity: <span class="fw-semibold text-dark">{{ number_format($history->bulk_quantity_received) }} pcs</span></div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Converted Items Card (Right Side) -->
        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0"><i class="ti ti-list-check me-2 text-success"></i>Converted Retail Items</h6>
                    <span class="badge bg-success">
                        {{ is_array($history->sorted_items) ? count($history->sorted_items) : 0 }} Types
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">#</th>
                                    <th width="50%">Retail Product</th>
                                    <th width="20%" class="text-end">Quantity</th>
                                    <th width="20%" class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sortedItems = is_array($history->sorted_items) ? $history->sorted_items : [];
                                    $totalQty = $history->bulk_quantity_received;
                                @endphp
                                @forelse($sortedItems as $index => $item)
                                    @php
                                        // রিটেইল প্রোডাক্টের নাম ডাটাবেস থেকে আনা
                                        $product = \App\Models\Product::find($item['product_id']);
                                        $productName = $product ? $product->name : 'Unknown Product (ID: '.$item['product_id'].')';
                                        $percentage = ($totalQty > 0) ? round(($item['quantity'] / $totalQty) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-medium">{{ $productName }}</td>
                                        <td class="text-end fw-bold">{{ number_format($item['quantity']) }} pcs</td>
                                        <td class="text-end">
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <div class="progress flex-grow-1" style="height: 6px; max-width: 100px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%"></div>
                                                </div>
                                                <span class="text-muted small">{{ $percentage }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No items found in this record.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="2" class="text-end">Total Converted:</td>
                                    <td class="text-end">{{ number_format($totalQty) }} pcs</td>
                                    <td class="text-end">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Section (If exists) -->
    @if($history->notes)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title mb-0"><i class="ti ti-notes me-2 text-warning"></i>Notes / Remarks</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0 text-muted">{{ nl2br(e($history->notes)) }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection