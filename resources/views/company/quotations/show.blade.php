@extends('layouts.admin_master')
@section('title', 'Quotation ' . $quotation->quotation_no)
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-file-description me-2 text-primary"></i>{{ $quotation->quotation_no }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('company.quotations.index') }}">Quotations</a></li>
                    <li class="breadcrumb-item active">{{ $quotation->quotation_no }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end d-flex gap-2 justify-content-end">
            <form method="POST" action="{{ route('company.quotations.update-status', $quotation->id) }}">
                @csrf @method('PATCH')
                <select name="status" class="form-select form-select-sm d-inline-block w-auto"
                    onchange="this.form.submit()">
                    @foreach (['draft', 'sent', 'accepted', 'rejected', 'expired'] as $s)
                        <option value="{{ $s }}" {{ $quotation->status == $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </form>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-printer me-1"></i>Print
            </button>
            <a href="{{ route('company.quotations.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card" id="quotation-print">
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="fw-bold">QUOTATION</h5>
                    <p class="text-muted mb-0">No: <strong>{{ $quotation->quotation_no }}</strong></p>
                    <p class="text-muted mb-0">Date: {{ $quotation->created_at->format('d M Y') }}</p>
                    @if ($quotation->valid_until)
                        <p class="text-muted mb-0">Valid Until: {{ $quotation->valid_until->format('d M Y') }}</p>
                    @endif
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-{{ $quotation->status_color }}-subtle text-{{ $quotation->status_color }} fs-6">
                        {{ ucfirst($quotation->status) }}
                    </span>
                    @if ($quotation->customer)
                        <div class="mt-2">
                            <strong>Bill To:</strong><br>
                            {{ $quotation->customer->name }}<br>
                            {{ $quotation->customer->phone ?? '' }}<br>
                            {{ $quotation->customer->email ?? '' }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quotation->items as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    {{ optional($item->variant->product)->name ?? '—' }}
                                    @if ($item->variant->name ?? false)
                                        <small class="text-muted">({{ $item->variant->name }})</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-end">৳{{ number_format($item->price, 2) }}</td>
                                <td class="text-end">৳{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Subtotal</th>
                            <th class="text-end">৳{{ number_format($quotation->subtotal, 2) }}</th>
                        </tr>
                        @if ($quotation->discount > 0)
                            <tr>
                                <th colspan="4" class="text-end text-danger">Discount</th>
                                <th class="text-end text-danger">-৳{{ number_format($quotation->discount, 2) }}</th>
                            </tr>
                        @endif
                        <tr class="table-primary">
                            <th colspan="4" class="text-end">Total</th>
                            <th class="text-end">৳{{ number_format($quotation->total_amount, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($quotation->notes)
                <div class="bg-light rounded p-3">
                    <strong>Notes:</strong>
                    <p class="mb-0 mt-1">{{ $quotation->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <form action="{{ route('company.quotations.destroy', $quotation->id) }}" method="POST" class="d-inline"
            onsubmit="return confirm('Delete this quotation?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="ti ti-trash me-1"></i>Delete Quotation
            </button>
        </form>
    </div>
@endsection
