@extends('layouts.admin_master') 

@section('title', 'Sorting History')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Sorting History</h4>
            <p class="text-muted mb-0">View all past bulk-to-retail sorting records for this branch.</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('branch.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('branch.inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sorting History</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('branch.inventory.receive-sort') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> New Sorting Entry
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- History Table Card -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($histories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">Date & Time</th>
                                <th width="15%">Reference</th>
                                <th width="25%">Bulk Item & Quantity</th>
                                <th width="15%">Converted Into</th>
                                <th width="15%">Sorted By</th>
                                <th width="10%" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($histories as $index => $history)
                                <tr>
                                    <td>{{ $histories->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $history->sorted_at->format('d M, Y') }}</div>
                                        <small class="text-muted">{{ $history->sorted_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        @if($history->reference_number)
                                            <span class="badge bg-light text-dark border">
                                                {{ $history->reference_number }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-primary">{{ $history->bulk_product_name }}</div>
                                        <small class="text-muted">{{ number_format($history->bulk_quantity_received) }} pcs</small>
                                    </td>
                                    <td>
                                        @php
                                            $sortedCount = is_array($history->sorted_items) ? count($history->sorted_items) : 0;
                                        @endphp
                                        <span class="badge bg-info-lt text-info">
                                            {{ $sortedCount }} Item(s)
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs bg-primary-lt text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 12px; font-weight: bold;">
                                                {{ strtoupper(substr($history->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <span class="fw-medium">{{ $history->user->name ?? 'Unknown' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('branch.inventory.sorting-history.show', $history->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $histories->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="ti ti-history text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                    <h5 class="text-muted">No sorting history found</h5>
                    <p class="text-muted mb-3">You haven't sorted any bulk items yet.</p>
                    <a href="{{ route('branch.inventory.receive-sort') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Create First Sorting Entry
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection