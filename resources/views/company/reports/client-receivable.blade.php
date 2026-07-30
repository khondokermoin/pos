@extends('layouts.admin_master')
@section('title', 'Client Receivable')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-arrow-bar-up me-2 text-primary"></i>Client Receivable</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Client Receivable</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 bg-success text-white">
                    <div class="card-body">
                        <p class="mb-1 opacity-75 small">Total Receivable</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($grandTotal, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Outstanding Receivables by Customer</h5>
            </div>
            <div class="card-body">
                @if ($customers->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-arrow-bar-up d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                        <h5 class="fw-semibold">No Customer Data</h5>
                        <p class="small">No customers found. Add customers and record sales to see receivables.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th class="text-end">Total Billed</th>
                                    <th class="text-end">Total Received</th>
                                    <th class="text-end">Balance Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $customer->name }}</strong></td>
                                        <td>{{ $customer->phone ?? '—' }}</td>
                                        <td class="text-end">{{ number_format($customer->total_billed ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($customer->total_received ?? 0, 2) }}</td>
                                        <td
                                            class="text-end fw-semibold {{ $customer->balance_due > 0 ? 'text-success' : 'text-muted' }}">
                                            {{ number_format($customer->balance_due, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="fw-bold text-end">Grand Total Receivable</td>
                                    <td class="text-end fw-bold text-success">{{ number_format($grandTotal, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
