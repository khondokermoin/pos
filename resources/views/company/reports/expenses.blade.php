@extends('layouts.admin_master')
@section('title', 'Expense Report')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-report-money me-2 text-primary"></i>Expense Report</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Expense Report</li>
                    </ol>
                </nav>
            </div>
        </div>

        <form method="GET" action="{{ route('company.reports.expenses') }}" class="card mb-4">
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

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 bg-danger text-white">
                    <div class="card-body">
                        <p class="mb-1 opacity-75 small">Total Expenses</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($totalExpenses, 2) }}</h4>
                        <small class="opacity-75">{{ $from }} to {{ $to }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Expense Details</h5>
                    </div>
                    <div class="card-body">
                        @if ($expenses->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="ti ti-report-money d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                                <h5 class="fw-semibold">No Expenses Found</h5>
                                <p class="small">No expenses recorded for the selected date range.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Branch</th>
                                            <th>Date</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($expenses as $expense)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $expense->title }}</td>
                                                <td><span
                                                        class="badge bg-secondary-lt text-secondary">{{ $expense->category }}</span>
                                                </td>
                                                <td>{{ $expense->branch->name ?? 'Head Office' }}</td>
                                                <td>{{ $expense->expense_date }}</td>
                                                <td class="text-end fw-semibold">{{ number_format($expense->amount, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $expenses->withQueryString()->links() }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">By Category</h5>
                    </div>
                    <div class="card-body">
                        @if ($byCategory->isEmpty())
                            <p class="text-muted text-center py-3">No data available.</p>
                        @else
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($byCategory as $cat)
                                        <tr>
                                            <td>{{ $cat->category }}</td>
                                            <td class="text-end">{{ number_format($cat->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
