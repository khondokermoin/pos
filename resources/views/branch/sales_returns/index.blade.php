@extends('layouts.admin_master')
@section('title', 'Sales Returns')
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-receipt-refund me-2 text-primary"></i>Sales Returns</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('branch.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Sales Returns</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('branch.sales-returns.create') }}" class="btn btn-primary btn-sm">
                <i class="ti ti-plus me-1"></i>New Return
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
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Return No</th>
                            <th>Original Sale</th>
                            <th>Customer</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $return)
                            <tr>
                                <td class="ps-3"><strong>{{ $return->return_no }}</strong></td>
                                <td>{{ optional($return->sale)->invoice_no ?? '—' }}</td>
                                <td>{{ optional(optional($return->sale)->customer)->name ?? 'Walk-in' }}</td>
                                <td class="text-end text-danger">৳{{ number_format($return->total_amount, 2) }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $return->status_color }}-subtle text-{{ $return->status_color }}">
                                        {{ ucfirst($return->status) }}
                                    </span>
                                </td>
                                <td><small>{{ $return->created_at->format('d M Y') }}</small></td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('branch.sales-returns.show', $return->id) }}"
                                        class="btn btn-sm btn-soft-primary">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="ti ti-receipt-refund d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                                    <h5>No Sales Returns</h5>
                                    <p class="small mb-3">No returns have been recorded yet.</p>
                                    <a href="{{ route('branch.sales-returns.create') }}" class="btn btn-sm btn-primary">
                                        <i class="ti ti-plus me-1"></i>Record First Return
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($returns->hasPages())
                <div class="px-3 py-2 border-top">
                    {{ $returns->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
