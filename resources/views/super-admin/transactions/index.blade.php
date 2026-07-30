@extends('layouts.admin_master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">Payment Transactions</h4>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Revenue</h6>
                        <h2 class="mb-0">৳{{ number_format($stats['total_revenue'], 2) }}</h2>
                    </div>
                    <div class="fs-1 opacity-50"><i class="ti ti-currency-taka"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Transactions</h6>
                        <h2 class="mb-0">{{ $stats['total_count'] }}</h2>
                    </div>
                    <div class="fs-1 opacity-50"><i class="ti ti-receipt"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Pending</h6>
                        <h2 class="mb-0">{{ $stats['pending_count'] }}</h2>
                    </div>
                    <div class="fs-1 opacity-50"><i class="ti ti-clock"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Failed</h6>
                        <h2 class="mb-0">{{ $stats['failed_count'] }}</h2>
                    </div>
                    <div class="fs-1 opacity-50"><i class="ti ti-x"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('superadmin.transactions.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Search Company</label>
                    <input type="text" name="search" class="form-control" placeholder="Company name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter me-1"></i> Filter</button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('superadmin.transactions.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Company</th>
                            <th>Plan</th>
                            <th>Transaction ID</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $txn)
                        <tr>
                            <td>{{ $txn->id }}</td>
                            <td>
                                <strong>{{ $txn->company->name ?? 'N/A' }}</strong>
                                <br><small class="text-muted">{{ $txn->company->email ?? '' }}</small>
                            </td>
                            <td>{{ $txn->subscription->plan->name ?? '—' }}</td>
                            <td><code>{{ $txn->transaction_id }}</code></td>
                            <td><strong>{{ $txn->currency }} {{ number_format($txn->amount, 2) }}</strong></td>
                            <td>{{ ucfirst($txn->payment_method ?? '—') }}</td>
                            <td>
                                @php
                                    $badge = match($txn->status) {
                                        'success' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        'refunded' => 'info',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ ucfirst($txn->status) }}</span>
                            </td>
                            <td>{{ $txn->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No transactions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }}
                </div>
                {{ $transactions->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
