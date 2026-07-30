@extends('layouts.admin_master')
@section('title', $employee->name)
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-user me-2 text-primary"></i>{{ $employee->name }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('company.employees.index') }}">Employees</a></li>
                    <li class="breadcrumb-item active">{{ $employee->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end d-flex gap-2 justify-content-end">
            <a href="{{ route('company.employees.edit', $employee->id) }}" class="btn btn-warning btn-sm">
                <i class="ti ti-pencil me-1"></i>Edit
            </a>
            <a href="{{ route('company.employees.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center py-4">
                    <div class="avatar-lg bg-primary-subtle rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                        style="width:80px;height:80px;">
                        <i class="ti ti-user fs-1 text-primary"></i>
                    </div>
                    <h5 class="mb-1">{{ $employee->name }}</h5>
                    <p class="text-muted mb-2">{{ $employee->designation ?? 'No Designation' }}</p>
                    <span class="badge bg-{{ $employee->status_color }}-subtle text-{{ $employee->status_color }}">
                        {{ ucfirst($employee->status) }}
                    </span>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Employee Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Department</td>
                            <td>{{ optional($employee->department)->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td>{{ $employee->email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone</td>
                            <td>{{ $employee->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Join Date</td>
                            <td>{{ $employee->join_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Current Salary</td>
                            <td class="fw-bold text-success">৳{{ number_format($employee->salary, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            {{-- Salary Increments --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="ti ti-trending-up me-2"></i>Salary Increments</h6>
                    <a href="{{ route('company.employees.increments') }}" class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-plus me-1"></i>Add Increment
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Effective Date</th>
                                    <th class="text-end">Amount</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->increments->sortByDesc('effective_date') as $inc)
                                    <tr>
                                        <td class="ps-3">{{ $inc->effective_date->format('d M Y') }}</td>
                                        <td class="text-end text-success">+৳{{ number_format($inc->amount, 2) }}</td>
                                        <td>{{ $inc->reason ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">No increments recorded</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Recent Payrolls --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="ti ti-file-invoice me-2"></i>Recent Payrolls</h6>
                    <a href="{{ route('company.payroll.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Month</th>
                                    <th class="text-end">Net Salary</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Payslip</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->payrolls->sortByDesc('month')->take(6) as $payroll)
                                    <tr>
                                        <td class="ps-3">
                                            {{ \Carbon\Carbon::createFromFormat('Y-m', $payroll->month)->format('M Y') }}
                                        </td>
                                        <td class="text-end">৳{{ number_format($payroll->net_salary, 2) }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $payroll->status_color }}-subtle text-{{ $payroll->status_color }}">
                                                {{ ucfirst($payroll->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('company.payroll.payslip', $payroll->id) }}"
                                                class="btn btn-sm btn-soft-primary" target="_blank">
                                                <i class="ti ti-file-invoice"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">No payroll records</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
