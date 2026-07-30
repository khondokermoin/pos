@extends('layouts.admin_master')
@section('title', 'Loans')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-coin me-2 text-primary"></i>Loans</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Loans</li>
                    </ol>
                </nav>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show"><i
                    class="ti ti-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button></div>
        @endif

        <div class="card">
            <div class="card-body">
                @if ($loans->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-coin d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                        <h5 class="fw-semibold">No Loans Recorded</h5>
                        <p class="small mb-3">Record loans taken from banks or other authorities.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Authority</th>
                                    <th>Amount</th>
                                    <th>Interest %</th>
                                    <th>Loan Date</th>
                                    <th>Due Date</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loans as $loan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $loan->authority->name ?? '—' }}</td>
                                        <td>{{ number_format($loan->amount, 2) }}</td>
                                        <td>{{ $loan->interest_rate ?? 0 }}%</td>
                                        <td>{{ $loan->loan_date ?? '—' }}</td>
                                        <td>{{ $loan->due_date ?? '—' }}</td>
                                        <td>{{ $loan->notes ?? '—' }}</td>
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
