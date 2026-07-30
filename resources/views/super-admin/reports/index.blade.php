@extends('layouts.admin_master')

@section('content')
    <div class="container-fluid">
        <div class="page-title-box d-flex justify-content-between align-items-center">
            <h4 class="page-title">Revenue Reports</h4>
        </div>

        {{-- Tab Navigation --}}
        <div class="mb-3">
            <a href="{{ route('superadmin.reports.index', ['type' => 'revenue']) }}" class="btn btn-primary btn-sm me-1">
                <i class="ti ti-chart-bar me-1"></i> Revenue Report
            </a>
            <a href="{{ route('superadmin.reports.index', ['type' => 'tenant-usage']) }}"
                class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-chart-pie me-1"></i> Tenant Usage
            </a>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from" class="form-control" value="{{ $from }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to" class="form-control" value="{{ $to }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('superadmin.reports.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6 class="text-white-50">Revenue</h6>
                        <h2>৳{{ number_format($revenue, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="text-white-50">New Subscriptions</h6>
                        <h2>{{ $newSubscriptions }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6 class="text-white-50">Cancelled</h6>
                        <h2>{{ $cancelledSubs }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6 class="text-white-50">New Companies</h6>
                        <h2>{{ $newCompanies }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Successful Transactions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Company</th>
                                <th>Plan</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $txn)
                                <tr>
                                    <td>{{ $txn->company->name ?? 'N/A' }}</td>
                                    <td>{{ $txn->subscription->plan->name ?? '—' }}</td>
                                    <td><strong>{{ $txn->currency }} {{ number_format($txn->amount, 2) }}</strong></td>
                                    <td>{{ ucfirst($txn->payment_method ?? '—') }}</td>
                                    <td>{{ $txn->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No transactions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $transactions->withQueryString()->links() }}</div>
            </div>
        </div>
    </div>
@endsection
