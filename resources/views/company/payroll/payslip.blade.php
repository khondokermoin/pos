@extends('layouts.admin_master')
@section('title', 'Payslip — ' . $payroll->employee->name)
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-file-invoice me-2 text-primary"></i>Payslip</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('company.payroll.index') }}">Payroll</a></li>
                    <li class="breadcrumb-item active">Payslip</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <i class="ti ti-printer me-1"></i>Print Payslip
            </button>
            <a href="{{ route('company.payroll.index', ['month' => $payroll->month]) }}"
                class="btn btn-outline-secondary btn-sm ms-1">
                <i class="ti ti-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="card" id="payslip-card">
        <div class="card-body p-4">
            {{-- Header --}}
            <div class="text-center border-bottom pb-3 mb-4">
                <h3 class="fw-bold mb-1">PAYSLIP</h3>
                <p class="text-muted mb-0">For the month of
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $payroll->month)->format('F Y') }}</p>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted text-uppercase small mb-2">Employee Details</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:130px;">Name</td>
                            <td><strong>{{ $payroll->employee->name }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Designation</td>
                            <td>{{ $payroll->employee->designation ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Department</td>
                            <td>{{ optional($payroll->employee->department)->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Join Date</td>
                            <td>{{ $payroll->employee->join_date->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted text-uppercase small mb-2">Payroll Details</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:130px;">Payroll ID</td>
                            <td><strong>#{{ $payroll->id }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Month</td>
                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $payroll->month)->format('F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td><span
                                    class="badge bg-{{ $payroll->status_color }}-subtle text-{{ $payroll->status_color }}">{{ ucfirst($payroll->status) }}</span>
                            </td>
                        </tr>
                        @if ($payroll->paid_at)
                            <tr>
                                <td class="text-muted">Paid On</td>
                                <td>{{ $payroll->paid_at->format('d M Y') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Salary Breakdown --}}
            <h6 class="text-muted text-uppercase small mb-2">Salary Breakdown</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Earnings</th>
                            <th class="text-end">Amount (৳)</th>
                            <th>Deductions</th>
                            <th class="text-end">Amount (৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td class="text-end">{{ number_format($payroll->basic_salary, 2) }}</td>
                            <td>Deductions</td>
                            <td class="text-end text-danger">{{ number_format($payroll->deduction, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Bonus</td>
                            <td class="text-end text-success">{{ number_format($payroll->bonus, 2) }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>Total Earnings</th>
                            <th class="text-end">৳{{ number_format($payroll->basic_salary + $payroll->bonus, 2) }}</th>
                            <th>Total Deductions</th>
                            <th class="text-end text-danger">৳{{ number_format($payroll->deduction, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Net Salary --}}
            <div class="bg-primary text-white rounded p-3 text-center">
                <h5 class="mb-0">Net Salary: <strong>৳{{ number_format($payroll->net_salary, 2) }}</strong></h5>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @media print {

            .sidenav-menu,
            .topbar,
            .page-title,
            nav,
            .btn,
            .breadcrumb {
                display: none !important;
            }

            #payslip-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

            body {
                background: white !important;
            }
        }
    </style>
@endpush
