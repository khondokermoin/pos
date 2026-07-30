@extends('layouts.admin_master')
@section('title', 'Balance Sheet')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-scale me-2 text-primary"></i>Balance Sheet</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Balance Sheet</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="ti ti-info-circle me-2 fs-5"></i>
            <div>
                Balance Sheet data will be fully populated once the <strong>Cash Accounts</strong>, <strong>Assets</strong>,
                and <strong>Loans</strong> modules are activated and data is entered.
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="ti ti-arrow-bar-up me-2"></i>Assets</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td>Cash & Bank Balances</td>
                                    <td class="text-end">0.00</td>
                                </tr>
                                <tr>
                                    <td>Fixed Assets (Net)</td>
                                    <td class="text-end">0.00</td>
                                </tr>
                                <tr>
                                    <td>Inventory Value</td>
                                    <td class="text-end">0.00</td>
                                </tr>
                                <tr>
                                    <td>Accounts Receivable</td>
                                    <td class="text-end">0.00</td>
                                </tr>
                            </tbody>
                            <tfoot class="table-success">
                                <tr>
                                    <td class="fw-bold">Total Assets</td>
                                    <td class="text-end fw-bold">{{ number_format($totalAssets, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="ti ti-arrow-bar-down me-2"></i>Liabilities & Equity</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td>Loans Payable</td>
                                    <td class="text-end">0.00</td>
                                </tr>
                                <tr>
                                    <td>Accounts Payable (Suppliers)</td>
                                    <td class="text-end">0.00</td>
                                </tr>
                                <tr class="table-light">
                                    <td class="fw-semibold">Owner's Equity</td>
                                    <td class="text-end fw-semibold text-primary">{{ number_format($equity, 2) }}</td>
                                </tr>
                            </tbody>
                            <tfoot class="table-danger">
                                <tr>
                                    <td class="fw-bold">Total Liabilities + Equity</td>
                                    <td class="text-end fw-bold">{{ number_format($totalLiabilities + $equity, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
