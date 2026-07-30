@extends('layouts.admin_master')
@section('title', 'Return ' . $return->return_no)
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-receipt-refund me-2 text-primary"></i>{{ $return->return_no }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('company.purchase-returns.index') }}">Purchase Returns</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $return->return_no }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end d-flex gap-2 justify-content-end">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-printer me-1"></i>Print
            </button>
            <a href="{{ route('company.purchase-returns.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h6 class="mb-0">Returned Items</h6>
                    <span class="badge bg-{{ $return->status_color }}-subtle text-{{ $return->status_color }}">
                        {{ ucfirst($return->status) }}
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end pe-3">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($return->items as $i => $item)
                                    <tr>
                                        <td class="ps-3">{{ $i + 1 }}</td>
                                        <td>{{ optional(optional($item->purchaseItem)->variant->product)->name ?? '—' }}
                                        </td>
                                        <td class="text-center">{{ $item->qty }}</td>
                                        <td class="text-end">৳{{ number_format($item->price, 2) }}</td>
                                        <td class="text-end pe-3">৳{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Total Return Value</th>
                                    <th class="text-end pe-3 text-danger">৳{{ number_format($return->total_amount, 2) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Return Info</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Return No</td>
                            <td><strong>{{ $return->return_no }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Purchase</td>
                            <td>{{ optional($return->purchase)->id ? '#' . $return->purchase->id : '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Supplier</td>
                            <td>{{ optional(optional($return->purchase)->supplier)->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Reason</td>
                            <td>{{ $return->reason }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date</td>
                            <td>{{ $return->created_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total</td>
                            <td class="text-danger fw-bold">৳{{ number_format($return->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('company.purchase-returns.destroy', $return->id) }}" method="POST"
                        onsubmit="return confirm('Delete this return?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="ti ti-trash me-1"></i>Delete Return
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
