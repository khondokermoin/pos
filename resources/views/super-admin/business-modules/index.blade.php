@extends('layouts.super-admin')
@section('title', 'Module Mapping')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Module Mapping</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Module Mapping</li>
                    </ol>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show"><i
                    class="ti ti-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button></div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show"><i
                    class="ti ti-alert-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row">
            <!-- Create Form -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ti ti-plus me-2"></i>Add Business Module</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('superadmin.business-modules.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Module Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="e.g. Payroll Management" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Brief description...">{{ old('description') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Icon Class</label>
                                <input type="text" name="icon" class="form-control" value="{{ old('icon') }}"
                                    placeholder="e.g. ti ti-users">
                                <div class="form-text">Tabler icon class (e.g. <code>ti ti-users</code>)</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Assign to Business Types</label>
                                <div class="border rounded p-2" style="max-height:150px;overflow-y:auto;">
                                    @foreach ($businessTypes as $bt)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="business_type_ids[]"
                                                value="{{ $bt->id }}" id="bt_{{ $bt->id }}">
                                            <label class="form-check-label"
                                                for="bt_{{ $bt->id }}">{{ $bt->name }}</label>
                                        </div>
                                    @endforeach
                                    @if ($businessTypes->isEmpty())
                                        <small class="text-muted">No business types yet. <a
                                                href="{{ route('superadmin.business-types.index') }}">Add some
                                                first.</a></small>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex gap-3 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_core" value="1"
                                        {{ old('is_core') ? 'checked' : '' }}>
                                    <label class="form-check-label">Core Module</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                    <label class="form-check-label">Active</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="ti ti-device-floppy me-1"></i>
                                Save Module</button>
                        </form>
                    </div>
                </div>
                <div class="alert alert-info border-0">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Core Modules</strong> are always enabled for all companies and cannot be deleted.
                    <strong>Non-core modules</strong> can be selectively enabled per company.
                </div>
            </div>

            <!-- Modules List -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ti ti-puzzle me-2 text-primary"></i>All Business Modules <span
                                class="badge bg-secondary ms-2">{{ $modules->total() }}</span></h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Module</th>
                                        <th>Business Types</th>
                                        <th>Core</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($modules as $module)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if ($module->icon)
                                                        <i class="{{ $module->icon }} fs-18 text-primary"></i>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $module->name }}</strong>
                                                        @if ($module->description)
                                                            <br><small
                                                                class="text-muted">{{ Str::limit($module->description, 40) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @foreach ($module->businessTypes->take(3) as $bt)
                                                    <span
                                                        class="badge bg-info-subtle text-info me-1">{{ $bt->name }}</span>
                                                @endforeach
                                                @if ($module->businessTypes->count() > 3)
                                                    <span
                                                        class="badge bg-secondary">+{{ $module->businessTypes->count() - 3 }}</span>
                                                @endif
                                                @if ($module->businessTypes->isEmpty())
                                                    <span class="text-muted small">All types</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($module->is_core)
                                                    <span class="badge bg-warning-subtle text-warning"><i
                                                            class="ti ti-lock me-1"></i>Core</span>
                                                @else
                                                    <span class="badge bg-light text-muted">Optional</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($module->is_active)
                                                    <span class="badge bg-success-subtle text-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-warning me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModule{{ $module->id }}">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                @if (!$module->is_core)
                                                    <form method="POST"
                                                        action="{{ route('superadmin.business-modules.destroy', $module) }}"
                                                        class="d-inline" onsubmit="return confirm('Delete this module?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"><i
                                                                class="ti ti-trash"></i></button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModule{{ $module->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit: {{ $module->name }}</h5><button
                                                            type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST"
                                                        action="{{ route('superadmin.business-modules.update', $module) }}">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3"><label
                                                                        class="form-label">Name</label><input
                                                                        type="text" name="name"
                                                                        class="form-control" value="{{ $module->name }}"
                                                                        required></div>
                                                                <div class="col-md-6 mb-3"><label
                                                                        class="form-label">Icon</label><input
                                                                        type="text" name="icon"
                                                                        class="form-control" value="{{ $module->icon }}">
                                                                </div>
                                                            </div>
                                                            <div class="mb-3"><label
                                                                    class="form-label">Description</label>
                                                                <textarea name="description" class="form-control" rows="2">{{ $module->description }}</textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Business Types</label>
                                                                <div class="border rounded p-2 d-flex flex-wrap gap-2">
                                                                    @foreach ($businessTypes as $bt)
                                                                        <div class="form-check">
                                                                            <input class="form-check-input"
                                                                                type="checkbox" name="business_type_ids[]"
                                                                                value="{{ $bt->id }}"
                                                                                {{ $module->businessTypes->contains($bt->id) ? 'checked' : '' }}>
                                                                            <label
                                                                                class="form-check-label">{{ $bt->name }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div class="d-flex gap-3">
                                                                <div class="form-check form-switch"><input
                                                                        class="form-check-input" type="checkbox"
                                                                        name="is_core" value="1"
                                                                        {{ $module->is_core ? 'checked' : '' }}><label
                                                                        class="form-check-label">Core Module</label></div>
                                                                <div class="form-check form-switch"><input
                                                                        class="form-check-input" type="checkbox"
                                                                        name="is_active" value="1"
                                                                        {{ $module->is_active ? 'checked' : '' }}><label
                                                                        class="form-check-label">Active</label></div>
                                                            </div>
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
                                            <td colspan="5" class="text-center py-4 text-muted"><i
                                                    class="ti ti-puzzle-off fs-24 d-block mb-2"></i>No business modules
                                                yet. Add your first module.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($modules->hasPages())
                        <div class="card-footer">{{ $modules->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
