@extends('layouts.super-admin')
@section('title', 'Global Attributes')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Global Attributes (Color / Size)</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Global Attributes</li>
                    </ol>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.global-categories.index') }}"><i
                        class="ti ti-category me-1"></i>Categories</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.global-units.index') }}"><i
                        class="ti ti-ruler me-1"></i>Units (UOM)</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.global-taxes.index') }}"><i
                        class="ti ti-receipt-tax me-1"></i>Taxes & VAT</a></li>
            <li class="nav-item"><a class="nav-link active" href="{{ route('superadmin.global-attributes.index') }}"><i
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
                        <h5 class="card-title mb-0"><i class="ti ti-plus me-2"></i>Add Attribute</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('superadmin.global-attributes.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Attribute Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="e.g. Color" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Values <span class="text-danger">*</span></label>
                                <input type="text" name="values"
                                    class="form-control @error('values') is-invalid @enderror" value="{{ old('values') }}"
                                    placeholder="Red, Blue, Green, Black (comma separated)" required>
                                <div class="form-text">Enter values separated by commas.</div>
                                @error('values')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                <label class="form-check-label">Active</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="ti ti-device-floppy me-1"></i>
                                Save Attribute</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ti ti-tags me-2 text-primary"></i>All Global Attributes <span
                                class="badge bg-secondary ms-2">{{ $attributes->total() }}</span></h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Values</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attributes as $attribute)
                                        <tr>
                                            <td><strong>{{ $attribute->name }}</strong></td>
                                            <td>
                                                @foreach (array_slice($attribute->values ?? [], 0, 5) as $val)
                                                    <span class="badge bg-light text-dark me-1">{{ $val }}</span>
                                                @endforeach
                                                @if (count($attribute->values ?? []) > 5)
                                                    <span class="badge bg-secondary">+{{ count($attribute->values) - 5 }}
                                                        more</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($attribute->is_active)
                                                    <span
                                                    class="badge bg-success-subtle text-success">Active</span>@else<span
                                                        class="badge bg-danger-subtle text-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-warning me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editAttr{{ $attribute->id }}"><i
                                                        class="ti ti-edit"></i></button>
                                                <form method="POST"
                                                    action="{{ route('superadmin.global-attributes.destroy', $attribute) }}"
                                                    class="d-inline" onsubmit="return confirm('Delete?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i
                                                            class="ti ti-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="editAttr{{ $attribute->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Attribute</h5><button type="button"
                                                            class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST"
                                                        action="{{ route('superadmin.global-attributes.update', $attribute) }}">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3"><label
                                                                    class="form-label">Name</label><input type="text"
                                                                    name="name" class="form-control"
                                                                    value="{{ $attribute->name }}" required></div>
                                                            <div class="mb-3"><label class="form-label">Values (comma
                                                                    separated)</label><input type="text" name="values"
                                                                    class="form-control"
                                                                    value="{{ implode(', ', $attribute->values ?? []) }}"
                                                                    required></div>
                                                            <div class="form-check form-switch"><input
                                                                    class="form-check-input" type="checkbox"
                                                                    name="is_active" value="1"
                                                                    {{ $attribute->is_active ? 'checked' : '' }}><label
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
                                                    class="ti ti-tags-off fs-24 d-block mb-2"></i>No global attributes yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($attributes->hasPages())
                        <div class="card-footer">{{ $attributes->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
