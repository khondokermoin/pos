@extends('layouts.admin_master')

@section('title', 'All Purchases')

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-shopping-cart me-2 text-primary"></i>All Purchases</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Purchases</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('company.purchases.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> New Purchase
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
    @php
        $totalAmount = $purchases->sum('total_amount');
        $thisMonthAmount = $purchases->filter(fn($p) => $p->created_at->isCurrentMonth())->sum('total_amount');
    @endphp
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Purchases</h6>
                        <h2 class="mb-0">{{ $purchases->total() }}</h2>
                    </div>
                    <i class="ti ti-shopping-cart fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Amount Spent</h6>
                        <h3 class="mb-0">৳{{ number_format($totalAmount, 2) }}</h3>
                    </div>
                    <i class="ti ti-currency-taka fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">This Month</h6>
                        <h3 class="mb-0">৳{{ number_format($thisMonthAmount, 2) }}</h3>
                    </div>
                    <i class="ti ti-calendar-stats fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('company.purchases.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                        value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                        value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Supplier name..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="ti ti-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('company.purchases.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="ti ti-x me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Purchases Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Purchase Date</th>
                            <th>Supplier</th>
                            <th>Branch</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr>
                                <td class="ps-3">{{ $purchases->firstItem() + $loop->index }}</td>
                                <td>
                                    <span class="fw-semibold">
                                        {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $purchase->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-warning bg-opacity-20 d-flex align-items-center justify-content-center"
                                            style="width:32px;height:32px;min-width:32px;">
                                            <span class="text-warning fw-bold small">
                                                {{ strtoupper(substr(optional($purchase->supplier)->name ?? 'S', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">
                                                {{ optional($purchase->supplier)->name ?? '—' }}</div>
                                            <small
                                                class="text-muted">{{ optional($purchase->supplier)->phone ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($purchase->branch)
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="ti ti-building-store me-1"></i>{{ $purchase->branch->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="ti ti-home me-1"></i>Central Warehouse
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ $purchase->items->count() }} item(s)
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-success">৳{{ number_format($purchase->total_amount, 2) }}</strong>
                                </td>
                                <td>
                                    @php
                                        $statusColor = match ($purchase->status ?? 'completed') {
                                            'completed' => 'success',
                                            'pending' => 'warning',
                                            'cancelled' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">
                                        {{ ucfirst($purchase->status ?? 'completed') }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ optional($purchase->user)->name ?? '—' }}</small>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('company.purchases.show', $purchase->id) }}"
                                        class="btn btn-sm btn-soft-primary" title="View Details">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="ti ti-shopping-cart-off d-block mb-3"
                                        style="font-size:3rem;opacity:0.4;"></i>
                                    <h5 class="fw-semibold">No Purchases Found</h5>
                                    @if (request()->hasAny(['date_from', 'date_to', 'search']))
                                        <p class="small mb-2">No results match your filter criteria.</p>
                                        <a href="{{ route('company.purchases.index') }}"
                                            class="btn btn-sm btn-outline-secondary">Clear Filters</a>
                                    @else
                                        <p class="small mb-3">No purchases have been recorded yet.</p>
                                        <a href="{{ route('company.purchases.create') }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-plus me-1"></i> Record First Purchase
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($purchases->hasPages())
                <div class="px-3 py-2 d-flex justify-content-between align-items-center border-top">
                    <div class="text-muted small">
                        Showing {{ $purchases->firstItem() }} to {{ $purchases->lastItem() }}
                        of {{ $purchases->total() }} purchases
                    </div>
                    {{ $purchases->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
