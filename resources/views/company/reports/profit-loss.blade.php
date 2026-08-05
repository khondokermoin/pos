@extends('layouts.admin_master')
@section('title', 'Profit & Loss')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-trending-up me-2 text-primary"></i>Profit & Loss</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profit & Loss</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Date Filter --}}
        <form method="GET" action="{{ route('company.reports.profit-loss') }}" class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">From Date</label>
                        <input type="date" name="from" class="form-control" value="{{ $from }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">To Date</label>
                        <input type="date" name="to" class="form-control" value="{{ $to }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-filter me-1"></i> Apply Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Summary Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 bg-success text-white">
                    <div class="card-body">
                        <p class="mb-1 opacity-75 small">Total Revenue</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($totalRevenue, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-warning text-dark">
                    <div class="card-body">
                        <p class="mb-1 opacity-75 small">Cost of Goods Sold</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($totalCogs, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-danger text-white">
                    <div class="card-body">
                        <p class="mb-1 opacity-75 small">Total Expenses</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($totalExpenses, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 {{ $netProfit >= 0 ? 'bg-primary' : 'bg-danger' }} text-white">
                    <div class="card-body">
                        <p class="mb-1 opacity-75 small">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</p>
                        <h4 class="mb-0 fw-bold">{{ number_format(abs($netProfit), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">P&L Summary — {{ $from }} to {{ $to }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tbody>
                        <tr class="table-success">
                            <td class="fw-semibold">Total Revenue (Sales)</td>
                            <td class="text-end fw-semibold">{{ number_format($totalRevenue, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted">Less: Cost of Goods Sold</td>
                            <td class="text-end text-danger">({{ number_format($totalCogs, 2) }})</td>
                        </tr>
                        <tr class="table-light">
                            <td class="fw-semibold">Gross Profit</td>
                            <td class="text-end fw-semibold {{ $grossProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($grossProfit, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted">Less: Operating Expenses</td>
                            <td class="text-end text-danger">({{ number_format($totalExpenses, 2) }})</td>
                        </tr>
                        <tr class="{{ $netProfit >= 0 ? 'table-success' : 'table-danger' }}">
                            <td class="fw-bold">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</td>
                            <td class="text-end fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($netProfit, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
