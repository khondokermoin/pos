@extends('layouts.admin_master')

@section('title', 'Quotations')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-file-description me-2 text-primary"></i>Quotations</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Quotations</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-6 text-sm-end">
                <a href="{{ route('company.quotations.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> New Quotation
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                @if ($quotations->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-file-description d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                        <h5 class="fw-semibold">No Quotations Yet</h5>
                        <p class="small mb-3">Create your first quotation to get started.</p>
                        <a href="{{ route('company.quotations.create') }}" class="btn btn-sm btn-primary">
                            <i class="ti ti-plus me-1"></i> Create Quotation
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Quotation No.</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Valid Until</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($quotations as $quotation)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $quotation->quotation_no ?? '—' }}</td>
                                        <td>{{ $quotation->customer->name ?? 'Walk-in' }}</td>
                                        <td>{{ number_format($quotation->total_amount ?? 0, 2) }}</td>
                                        <td>{{ $quotation->valid_until ?? '—' }}</td>
                                        <td><span class="badge bg-info-lt text-info">Draft</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('company.quotations.show', $quotation->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <form action="{{ route('company.quotations.destroy', $quotation->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this quotation?')">
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
