@extends('layouts.admin_master')

@section('title', 'Stock Transfers')

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-transfer me-2 text-primary"></i>Stock Transfers</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Stock Transfers</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('company.transfers.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> New Transfer
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats Row --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Transfers</h6>
                        <h2 class="mb-0">{{ $totalTransfers }}</h2>
                    </div>
                    <i class="ti ti-transfer fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Completed</h6>
                        <h2 class="mb-0">{{ $completedCount }}</h2>
                    </div>
                    <i class="ti ti-circle-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Pending</h6>
                        <h2 class="mb-0">{{ $pendingCount }}</h2>
                    </div>
                    <i class="ti ti-clock fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Transfers Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Reference</th>
                            <th>Date</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>By</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $transfer)
                            @php
                                $statusColor = match ($transfer->status) {
                                    'completed' => 'success',
                                    'pending' => 'warning',
                                    'cancelled' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td class="ps-3">{{ $transfers->firstItem() + $loop->index }}</td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">
                                        {{ $transfer->reference_no ?? '#' . $transfer->id }}
                                    </code>
                                </td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d M Y') }}</small>
                                </td>
                                <td>
                                    @if ($transfer->fromBranch)
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="ti ti-building-store me-1"></i>{{ $transfer->fromBranch->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-dark-subtle text-dark">
                                            <i class="ti ti-home me-1"></i>Central Warehouse
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">
                                        <i class="ti ti-building-store me-1"></i>
                                        {{ optional($transfer->toBranch)->name ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusColor }}">
                                        {{ ucfirst($transfer->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block text-muted small" style="max-width:120px;"
                                        title="{{ $transfer->note }}">
                                        {{ $transfer->note ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ optional($transfer->user)->name ?? '—' }}</small>
                                </td>
                                <td class="text-end pe-3">
                                    <span class="badge bg-light text-muted border">
                                        <i class="ti ti-check me-1"></i>Done
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="ti ti-transfer-out d-block mb-3" style="font-size:3rem;opacity:0.4;"></i>
                                    <h5 class="fw-semibold">No Transfers Found</h5>
                                    <p class="small mb-3">No stock transfers have been executed yet.</p>
                                    <a href="{{ route('company.transfers.create') }}" class="btn btn-sm btn-primary">
                                        <i class="ti ti-plus me-1"></i> Create First Transfer
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transfers->hasPages())
                <div class="px-3 py-2 d-flex justify-content-between align-items-center border-top">
                    <div class="text-muted small">
                        Showing {{ $transfers->firstItem() }} to {{ $transfers->lastItem() }}
                        of {{ $transfers->total() }} transfers
                    </div>
                    {{ $transfers->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
