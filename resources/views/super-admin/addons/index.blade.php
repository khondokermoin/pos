@extends('layouts.super-admin')
@section('title', 'Addons')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ti ti-puzzle me-2 text-primary"></i>Addons</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Addons</li>
                    </ol>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Add Addon Form -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ti ti-plus me-2"></i>Add New Addon</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('superadmin.addons.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Addon Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="e.g. Advanced Reports" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Brief description of this addon...">{{ old('description') }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label">Version</label>
                                    <input type="text" name="version" class="form-control"
                                        value="{{ old('version', '1.0.0') }}" placeholder="1.0.0">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Price ($)</label>
                                    <input type="number" name="price" class="form-control"
                                        value="{{ old('price', '0') }}" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                <label class="form-check-label">Active</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-device-floppy me-1"></i> Save Addon
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body text-center">
                        <i class="ti ti-shopping-cart fs-36 text-primary mb-2 d-block"></i>
                        <h6>Browse Marketplace</h6>
                        <p class="text-muted small">Discover and install new addons from the marketplace.</p>
                        <a href="{{ route('superadmin.addons.marketplace') }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-world me-1"></i> Go to Marketplace
                        </a>
                    </div>
                </div>
            </div>

            <!-- Addons List -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-puzzle me-2 text-primary"></i>Installed Addons
                            <span class="badge bg-secondary ms-2">{{ $addons->total() }}</span>
                        </h5>
                        <a href="{{ route('superadmin.addons.marketplace') }}" class="btn btn-sm btn-outline-primary">
                            <i class="ti ti-world me-1"></i> Marketplace
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Version</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($addons as $addon)
                                        <tr>
                                            <td>
                                                <strong>{{ $addon->name }}</strong>
                                                @if ($addon->description)
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($addon->description, 60) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">
                                                    v{{ $addon->version ?? '1.0.0' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($addon->price > 0)
                                                    <span
                                                        class="text-success fw-semibold">${{ number_format($addon->price, 2) }}</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success">Free</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($addon->is_active)
                                                    <span class="badge bg-success-subtle text-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-warning me-1"
                                                    data-bs-toggle="modal" data-bs-target="#editAddon{{ $addon->id }}">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <form method="POST"
                                                    action="{{ route('superadmin.addons.destroy', $addon) }}"
                                                    class="d-inline" onsubmit="return confirm('Remove this addon?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editAddon{{ $addon->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit: {{ $addon->name }}</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST"
                                                        action="{{ route('superadmin.addons.update', $addon) }}">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Name</label>
                                                                <input type="text" name="name" class="form-control"
                                                                    value="{{ $addon->name }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Description</label>
                                                                <textarea name="description" class="form-control" rows="3">{{ $addon->description }}</textarea>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-6 mb-3">
                                                                    <label class="form-label">Version</label>
                                                                    <input type="text" name="version"
                                                                        class="form-control"
                                                                        value="{{ $addon->version }}">
                                                                </div>
                                                                <div class="col-6 mb-3">
                                                                    <label class="form-label">Price ($)</label>
                                                                    <input type="number" name="price"
                                                                        class="form-control" value="{{ $addon->price }}"
                                                                        min="0" step="0.01">
                                                                </div>
                                                            </div>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="is_active" value="1"
                                                                    {{ $addon->is_active ? 'checked' : '' }}>
                                                                <label class="form-check-label">Active</label>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Update</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="ti ti-puzzle-off fs-36 d-block mb-2 opacity-50"></i>
                                                <h6>No Addons Installed</h6>
                                                <p class="small mb-3">Add your first addon or browse the marketplace.</p>
                                                <a href="{{ route('superadmin.addons.marketplace') }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="ti ti-world me-1"></i> Browse Marketplace
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($addons->hasPages())
                        <div class="card-footer">{{ $addons->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
