@extends('layouts.admin_master')
@section('title', 'Supplier Payable')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-arrow-bar-down me-2 text-primary"></i>Supplier Payable</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Supplier Payable</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 bg-danger text-white">
                    <div class="card-body">
                        <p class="mb-1 opacity-75 small">Total Payable</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($grandTotal, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Outstanding Payables by Supplier</h5>
            </div>
            <div class="card-body">
                @if ($suppliers->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-arrow-bar-down d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                        <h5 class="fw-semibold">No Supplier Data</h5>
                        <p class="small">No suppliers found. Add suppliers and record purchases to see payables.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Supplier</th>
                                    <th>Phone</th>
                                    <th class="text-end">Total Purchased</th>
                                    <th class="text-end">Total Paid</th>
                                    <th class="text-end">Balance Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($suppliers as $supplier)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $supplier->name }}</strong></td>
                                        <td>{{ $supplier->phone ?? '—' }}</td>
                                        <td class="text-end">{{ number_format($supplier->total_purchased ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($supplier->total_paid ?? 0, 2) }}</td>
                                        <td
                                            class="text-end fw-semibold {{ $supplier->balance_due > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($supplier->balance_due, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="fw-bold text-end">Grand Total Payable</td>
                                    <td class="text-end fw-bold text-danger">{{ number_format($grandTotal, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
