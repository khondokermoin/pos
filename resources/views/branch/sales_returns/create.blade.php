@extends('layouts.admin_master')
@section('title', 'New Sales Return')
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-receipt-refund me-2 text-primary"></i>New Sales Return</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('branch.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('branch.sales-returns.index') }}">Sales Returns</a></li>
                    <li class="breadcrumb-item active">New Return</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('branch.sales-returns.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('branch.sales-returns.store') }}" id="returnForm">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Select Original Sale</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sale Invoice <span class="text-danger">*</span></label>
                            <select name="sale_id" id="saleSelect" class="form-select" required>
                                <option value="">— Select Sale —</option>
                                @foreach ($sales as $sale)
                                    <option value="{{ $sale->id }}">
                                        {{ $sale->invoice_no }} —
                                        {{ optional($sale->customer)->name ?? 'Walk-in' }} —
                                        ৳{{ number_format($sale->total_amount, 2) }} —
                                        {{ $sale->created_at->format('d M Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="2" placeholder="Reason for return..." required>{{ old('reason') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card" id="itemsCard" style="display:none;">
                    <div class="card-header">
                        <h6 class="mb-0">Select Items to Return</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Return?</th>
                                        <th>Product</th>
                                        <th class="text-center">Sold Qty</th>
                                        <th class="text-center">Return Qty</th>
                                        <th class="text-end pe-3">Unit Price</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-check me-1"></i>Submit Return
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.getElementById('saleSelect').addEventListener('change', function() {
            const saleId = this.value;
            if (!saleId) {
                document.getElementById('itemsCard').style.display = 'none';
                return;
            }

            fetch(`{{ url('branch/sales-returns/sale') }}/${saleId}/items`)
                .then(r => r.json())
                .then(items => {
                    const tbody = document.getElementById('itemsBody');
                    tbody.innerHTML = '';
                    items.forEach((item, i) => {
                        tbody.innerHTML += `
                    <tr>
                        <td class="ps-3">
                            <input type="checkbox" name="items[${i}][sale_item_id]" value="${item.id}"
                                class="form-check-input" checked>
                        </td>
                        <td>${item.variant?.product?.name ?? '—'}</td>
                        <td class="text-center">${item.quantity}</td>
                        <td class="text-center">
                            <input type="number" name="items[${i}][qty]" value="1"
                                min="1" max="${item.quantity}" class="form-control form-control-sm text-center" style="width:70px;">
                        </td>
                        <td class="text-end pe-3">৳${parseFloat(item.price).toFixed(2)}</td>
                    </tr>`;
                    });
                    document.getElementById('itemsCard').style.display = 'block';
                });
        });
    </script>
@endpush
