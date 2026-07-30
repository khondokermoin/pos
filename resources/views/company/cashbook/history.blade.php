@extends('layouts.admin_master')
@section('title', 'Cash Book — History')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-history me-2 text-primary"></i>Transaction History</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Cash Book — History</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if ($transactions->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-history d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                        <h5 class="fw-semibold">No Transactions Yet</h5>
                        <p class="small">All cash movements (income, expenses, transfers) will appear here once recorded.
                        </p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Account</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $txn)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $txn->date ?? '—' }}</td>
                                        <td>{{ $txn->account->name ?? '—' }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $txn->type === 'credit' ? 'bg-success-lt text-success' : 'bg-danger-lt text-danger' }}">
                                                {{ ucfirst($txn->type ?? '—') }}
                                            </span>
                                        </td>
                                        <td>{{ $txn->description ?? '—' }}</td>
                                        <td
                                            class="text-end fw-semibold {{ ($txn->type ?? '') === 'credit' ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($txn->amount ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
