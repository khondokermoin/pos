@extends('layouts.admin_master')

@section('title', 'Record Purchase Return')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-truck-return me-2 text-primary"></i>Record Purchase Return</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('company.purchase-returns.index') }}">Purchase
                                Returns</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-6 text-sm-end">
                <a href="{{ route('company.purchase-returns.index') }}" class="btn btn-outline-secondary">
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

        <form action="{{ route('company.purchase-returns.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Return Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Original Purchase <span
                                    class="text-danger">*</span></label>
                            <select name="purchase_id" class="form-select @error('purchase_id') is-invalid @enderror"
                                required>
                                <option value="">— Select Purchase —</option>
                                @foreach ($purchases as $purchase)
                                    <option value="{{ $purchase->id }}"
                                        {{ old('purchase_id') == $purchase->id ? 'selected' : '' }}>
                                        #{{ $purchase->reference_no ?? $purchase->id }} —
                                        {{ $purchase->supplier->name ?? 'Unknown Supplier' }} —
                                        {{ $purchase->created_at->format('d M Y') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('purchase_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
                            <input type="text" name="reason" class="form-control @error('reason') is-invalid @enderror"
                                value="{{ old('reason') }}" placeholder="e.g., Wrong item delivered" required>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Items to Return</label>
                            <div class="row g-2 mb-2">
                                <div class="col-md-8">
                                    <input type="number" name="items[0][purchase_item_id]"
                                        class="form-control form-control-sm" placeholder="Purchase Item ID" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="items[0][qty]" class="form-control form-control-sm"
                                        placeholder="Qty" min="1" value="1" required>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="ti ti-info-circle me-1"></i>
                                Full item-level return UI will be available once the purchase_returns table is migrated.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('company.purchase-returns.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Record Return
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
