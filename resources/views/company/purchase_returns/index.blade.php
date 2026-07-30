@extends('layouts.admin_master')

@section('title', 'Purchase Returns')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-truck-return me-2 text-primary"></i>Purchase Returns</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Purchase Returns</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-6 text-sm-end">
                <a href="{{ route('company.purchase-returns.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> New Return
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
                @if ($returns->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-truck-return d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                        <h5 class="fw-semibold">No Purchase Returns Yet</h5>
                        <p class="small mb-3">Record a return when you send items back to a supplier.</p>
                        <a href="{{ route('company.purchase-returns.create') }}" class="btn btn-sm btn-primary">
                            <i class="ti ti-plus me-1"></i> Record Return
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Return No.</th>
                                    <th>Original Purchase</th>
                                    <th>Supplier</th>
                                    <th>Reason</th>
                                    <th>Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($returns as $return)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $return->return_no ?? '—' }}</td>
                                        <td>{{ $return->purchase->reference_no ?? '—' }}</td>
                                        <td>{{ $return->purchase->supplier->name ?? '—' }}</td>
                                        <td>{{ $return->reason ?? '—' }}</td>
                                        <td>{{ $return->created_at?->format('d M Y') ?? '—' }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('company.purchase-returns.destroy', $return->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this return?')">
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
