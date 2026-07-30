@extends('layouts.admin_master')
@section('title', 'Assets')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-building-warehouse me-2 text-primary"></i>Assets</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Assets</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-6 text-sm-end">
                <a href="{{ route('company.assets.types') }}" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="ti ti-category me-1"></i> Manage Types
                </a>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show"><i
                    class="ti ti-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row g-3">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Add Asset</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('company.assets.assets.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="e.g., Toyota Hiace" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Asset Type <span class="text-danger">*</span></label>
                                <select name="asset_type_id" class="form-select" required>
                                    <option value="">— Select Type —</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('asset_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Purchase Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="purchase_date" class="form-control"
                                    value="{{ old('purchase_date', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Purchase Price <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="purchase_price" class="form-control" min="0"
                                    step="0.01" value="{{ old('purchase_price', 0) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Current Value</label>
                                <input type="number" name="current_value" class="form-control" min="0"
                                    step="0.01" value="{{ old('current_value') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="ti ti-plus me-1"></i> Add
                                Asset</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All Assets</h5>
                    </div>
                    <div class="card-body">
                        @if ($assets->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="ti ti-building-warehouse d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                                <h5 class="fw-semibold">No Assets Yet</h5>
                                <p class="small">Add company assets like vehicles, machinery, and equipment.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Purchase Price</th>
                                            <th>Current Value</th>
                                            <th>Date</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($assets as $asset)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><strong>{{ $asset->name }}</strong></td>
                                                <td>{{ $asset->type->name ?? '—' }}</td>
                                                <td>{{ number_format($asset->purchase_price, 2) }}</td>
                                                <td>{{ $asset->current_value ? number_format($asset->current_value, 2) : '—' }}
                                                </td>
                                                <td>{{ $asset->purchase_date ?? '—' }}</td>
                                                <td class="text-end">
                                                    <form action="{{ route('company.assets.assets.destroy', $asset->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Delete this asset?')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger"><i
                                                                class="ti ti-trash"></i></button>
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
        </div>
    </div>
@endsection
