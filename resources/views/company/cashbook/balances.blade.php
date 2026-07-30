@extends('layouts.admin_master')
@section('title', 'Cash Book — Balances')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-wallet me-2 text-primary"></i>Account Balances</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Cash Book — Balances</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="mb-1 opacity-75 small">Total Balance</p>
                                <h3 class="mb-0 fw-bold">{{ number_format($totalBalance, 2) }}</h3>
                            </div>
                            <i class="ti ti-cash" style="font-size:2.5rem;opacity:.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Balance Summary</h5>
                <a href="{{ route('company.cashbook.accounts') }}" class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-settings me-1"></i> Manage Accounts
                </a>
            </div>
            <div class="card-body">
                @if ($accounts->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-wallet d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                        <h5 class="fw-semibold">No Accounts Found</h5>
                        <p class="small mb-3">Create cash or bank accounts first to see balances here.</p>
                        <a href="{{ route('company.cashbook.accounts') }}" class="btn btn-sm btn-primary">
                            <i class="ti ti-plus me-1"></i> Add Account
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Account</th>
                                    <th>Type</th>
                                    <th class="text-end">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($accounts as $account)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $account->name }}</strong></td>
                                        <td><span
                                                class="badge bg-secondary-lt text-secondary">{{ ucfirst(str_replace('_', ' ', $account->type)) }}</span>
                                        </td>
                                        <td
                                            class="text-end fw-semibold {{ $account->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($account->balance, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="fw-bold text-end">Total</td>
                                    <td class="text-end fw-bold">{{ number_format($totalBalance, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
