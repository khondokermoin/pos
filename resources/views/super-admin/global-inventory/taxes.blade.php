@extends('layouts.super-admin')
@section('title', 'Global Taxes & VAT')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Global Taxes & VAT</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Global Taxes</li>
                    </ol>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.global-categories.index') }}"><i
                        class="ti ti-category me-1"></i>Categories</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.global-units.index') }}"><i
                        class="ti ti-ruler me-1"></i>Units (UOM)</a></li>
            <li class="nav-item"><a class="nav-link active" href="{{ route('superadmin.global-taxes.index') }}"><i
                        class="ti ti-receipt-tax me-1"></i>Taxes & VAT</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.global-attributes.index') }}"><i
                        class="ti ti-tags me-1"></i>Attributes</a></li>
        </ul>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show"><i
                    class="ti ti-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ti ti-plus me-2"></i>Add Tax / VAT</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('superadmin.global-taxes.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Tax Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="e.g. Standard VAT" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rate (%) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="rate"
                                        class="form-control @error('rate') is-invalid @enderror"
                                        value="{{ old('rate', 0) }}" step="0.01" min="0" max="100" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                @error('rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                <label class="form-check-label">Active</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="ti ti-device-floppy me-1"></i>
                                Save Tax</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ti ti-receipt-tax me-2 text-primary"></i>All Global Taxes
                            <span class="badge bg-secondary ms-2">{{ $taxes->total() }}</span></h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Rate</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($taxes as $tax)
                                        <tr>
                                            <td><strong>{{ $tax->name }}</strong></td>
                                            <td><span
                                                    class="badge bg-warning-subtle text-warning fs-13">{{ $tax->rate }}%</span>
                                            </td>
                                            <td>
                                                @if ($tax->is_active)
                                                    <span
                                                    class="badge bg-success-subtle text-success">Active</span>@else<span
                                                        class="badge bg-danger-subtle text-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-warning me-1"
                                                    data-bs-toggle="modal" data-bs-target="#editTax{{ $tax->id }}"><i
                                                        class="ti ti-edit"></i></button>
                                                <form method="POST"
                                                    action="{{ route('superadmin.global-taxes.destroy', $tax) }}"
                                                    class="d-inline" onsubmit="return confirm('Delete?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i
                                                            class="ti ti-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="editTax{{ $tax->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Tax</h5><button type="button"
                                                            class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST"
                                                        action="{{ route('superadmin.global-taxes.update', $tax) }}">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3"><label
                                                                    class="form-label">Name</label><input type="text"
                                                                    name="name" class="form-control"
                                                                    value="{{ $tax->name }}" required></div>
                                                            <div class="mb-3"><label class="form-label">Rate (%)</label>
                                                                <div class="input-group"><input type="number"
                                                                        name="rate" class="form-control"
                                                                        value="{{ $tax->rate }}" step="0.01"
                                                                        min="0" max="100" required><span
                                                                        class="input-group-text">%</span></div>
                                                            </div>
                                                            <div class="form-check form-switch"><input
                                                                    class="form-check-input" type="checkbox"
                                                                    name="is_active" value="1"
                                                                    {{ $tax->is_active ? 'checked' : '' }}><label
                                                                    class="form-check-label">Active</label></div>
                                                        </div>
                                                        <div class="modal-footer"><button type="button"
                                                                class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button><button
                                                                type="submit" class="btn btn-primary">Update</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted"><i
                                                    class="ti ti-receipt-off fs-24 d-block mb-2"></i>No global taxes yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($taxes->hasPages())
                        <div class="card-footer">{{ $taxes->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
