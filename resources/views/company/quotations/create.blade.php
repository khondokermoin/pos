@extends('layouts.admin_master')

@section('title', 'New Quotation')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-file-description me-2 text-primary"></i>New Quotation</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('company.quotations.index') }}">Quotations</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-6 text-sm-end">
                <a href="{{ route('company.quotations.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('company.quotations.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quotation Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer (Optional)</label>
                            <select name="customer_id" class="form-select">
                                <option value="">— Walk-in Customer —</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Valid Until</label>
                            <input type="date" name="valid_until" class="form-control"
                                value="{{ old('valid_until', now()->addDays(30)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes for the customer...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-semibold mb-3">Items</h6>

                    <div id="quotation-items">
                        <div class="row g-2 mb-2 align-items-end quotation-item-row">
                            <div class="col-md-5">
                                <label class="form-label small">Product / Variant</label>
                                <select name="items[0][variant_id]" class="form-select form-select-sm" required>
                                    <option value="">— Select Product —</option>
                                    @foreach ($products as $variant)
                                        <option value="{{ $variant->id }}">
                                            {{ $variant->product->name ?? '' }} — {{ $variant->name ?? 'Default' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Qty</label>
                                <input type="number" name="items[0][qty]" class="form-control form-control-sm"
                                    value="1" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Unit Price</label>
                                <input type="number" name="items[0][price]" class="form-control form-control-sm"
                                    value="0" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" disabled>
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-item-btn" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="ti ti-plus me-1"></i> Add Item
                    </button>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('company.quotations.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Save Quotation
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let itemIndex = 1;
        document.getElementById('add-item-btn').addEventListener('click', function() {
            const container = document.getElementById('quotation-items');
            const row = document.querySelector('.quotation-item-row').cloneNode(true);
            row.querySelectorAll('select, input').forEach(el => {
                el.name = el.name.replace(/\[\d+\]/, '[' + itemIndex + ']');
                if (el.tagName === 'INPUT') el.value = el.type === 'number' ? (el.name.includes('qty') ? 1 :
                    0) : '';
            });
            row.querySelector('.remove-item-btn').disabled = false;
            row.querySelector('.remove-item-btn').addEventListener('click', function() {
                row.remove();
            });
            container.appendChild(row);
            itemIndex++;
        });
    </script>
@endpush
