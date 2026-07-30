@extends('layouts.admin_master')
@section('title', 'Payroll')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-cash me-2 text-primary"></i>Payroll</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Payroll</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-6 text-sm-end">
                <form action="{{ route('company.payroll.generate') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="month" name="month" class="form-control form-control-sm d-inline-block"
                        style="width:160px;" value="{{ $month }}">
                    <button type="submit" class="btn btn-primary btn-sm ms-2">
                        <i class="ti ti-refresh me-1"></i> Generate Payroll
                    </button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Payroll — {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h5>
            </div>
            <div class="card-body">
                @if ($payrolls->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-cash d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                        <h5 class="fw-semibold">No Payroll Records</h5>
                        <p class="small mb-3">
                            Click <strong>Generate Payroll</strong> to create payroll entries for all active employees
                            for the selected month.
                        </p>
                        <small class="text-muted">
                            <i class="ti ti-info-circle me-1"></i>
                            Payroll generation requires the employees table to be migrated first.
                        </small>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th class="text-end">Basic Salary</th>
                                    <th class="text-end">Deductions</th>
                                    <th class="text-end">Net Pay</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payrolls as $payroll)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $payroll->employee->name ?? '—' }}</strong></td>
                                        <td>{{ $payroll->employee->department->name ?? '—' }}</td>
                                        <td class="text-end">{{ number_format($payroll->basic_salary ?? 0, 2) }}</td>
                                        <td class="text-end text-danger">{{ number_format($payroll->deductions ?? 0, 2) }}
                                        </td>
                                        <td class="text-end fw-semibold text-success">
                                            {{ number_format($payroll->net_pay ?? 0, 2) }}</td>
                                        <td>
                                            <span
                                                class="badge {{ ($payroll->status ?? '') === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                {{ ucfirst($payroll->status ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @if (($payroll->status ?? '') !== 'paid')
                                                <form action="{{ route('company.payroll.mark-paid', $payroll->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-success me-1">
                                                        <i class="ti ti-check"></i> Mark Paid
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('company.payroll.payslip', $payroll->id) }}"
                                                class="btn btn-sm btn-outline-primary me-1" target="_blank">
                                                <i class="ti ti-printer"></i>
                                            </a>
                                            <form action="{{ route('company.payroll.destroy', $payroll->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this payroll entry?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
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
