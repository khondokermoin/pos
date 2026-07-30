@extends('layouts.super-admin')
@section('title', 'Invoice Templates')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Invoice Templates</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Invoice Templates</li>
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
                        <h5 class="card-title mb-0"><i class="ti ti-plus me-2"></i>Add New Template</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('superadmin.invoice-templates.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Template Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="e.g. Standard POS Receipt" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="pos" {{ old('type') == 'pos' ? 'selected' : '' }}>POS / Thermal</option>
                                    <option value="a4" {{ old('type') == 'a4' ? 'selected' : '' }}>A4 Paper</option>
                                    <option value="thermal" {{ old('type') == 'thermal' ? 'selected' : '' }}>Thermal 80mm</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">HTML Content</label>
                                <textarea name="html_content" class="form-control" rows="8"
                                    placeholder="Enter HTML template content with {{ variables }}...">{{ old('html_content') }}</textarea>
                                <div class="form-text">Available variables: <code>{{ company_name }}</code>,
                                    <code>{{ invoice_no }}</code>, <code>{{ date }}</code>,
                                    <code>{{ items }}</code>, <code>{{ total }}</code></div>
                            </div>
                            <div class="mb-3 d-flex gap-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_default" id="is_default"
                                        value="1" {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">Set as Default</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                        value="1" checked>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="ti ti-device-floppy me-1"></i>
                                Save Template</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Templates List -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ti ti-receipt me-2 text-primary"></i>All Templates <span
                                class="badge bg-secondary ms-2">{{ $templates->total() }}</span></h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Default</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($templates as $template)
                                        <tr>
                                            <td><strong>{{ $template->name }}</strong></td>
                                            <td><span
                                                    class="badge bg-info-subtle text-info">{{ $template->getTypeLabel() }}</span>
                                            </td>
                                            <td>
                                                @if ($template->is_default)
                                                    <span class="badge bg-success"><i
                                                            class="ti ti-star-filled me-1"></i>Default</span>
                                                @else
                                                    <form method="POST"
                                                        action="{{ route('superadmin.invoice-templates.set-default', $template) }}"
                                                        class="d-inline">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="btn btn-xs btn-outline-secondary">Set
                                                            Default</button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($template->is_active)
                                                    <span class="badge bg-success-subtle text-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $template->created_at->format('d M Y') }}</td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-warning me-1"
                                                    data-bs-toggle="modal" data-bs-target="#editModal{{ $template->id }}">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <form method="POST"
                                                    action="{{ route('superadmin.invoice-templates.destroy', $template) }}"
                                                    class="d-inline" onsubmit="return confirm('Delete this template?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i
                                                            class="ti ti-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal{{ $template->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit: {{ $template->name }}</h5><button
                                                            type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST"
                                                        action="{{ route('superadmin.invoice-templates.update', $template) }}">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3"><label
                                                                    class="form-label">Name</label><input type="text"
                                                                    name="name" class="form-control"
                                                                    value="{{ $template->name }}" required></div>
                                                            <div class="mb-3"><label class="form-label">Type</label>
                                                                <select name="type" class="form-select">
                                                                    <option value="pos"
                                                                        {{ $template->type == 'pos' ? 'selected' : '' }}>POS /
                                                                        Thermal</option>
                                                                    <option value="a4"
                                                                        {{ $template->type == 'a4' ? 'selected' : '' }}>A4 Paper
                                                                    </option>
                                                                    <option value="thermal"
                                                                        {{ $template->type == 'thermal' ? 'selected' : '' }}>
                                                                        Thermal 80mm</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3"><label class="form-label">HTML
                                                                    Content</label>
                                                                <textarea name="html_content" class="form-control" rows="10">{{ $template->html_content }}</textarea>
                                                            </div>
                                                            <div class="d-flex gap-3">
                                                                <div class="form-check form-switch"><input
                                                                        class="form-check-input" type="checkbox"
                                                                        name="is_default" value="1"
                                                                        {{ $template->is_default ? 'checked' : '' }}><label
                                                                        class="form-check-label">Default</label></div>
                                                                <div class="form-check form-switch"><input
                                                                        class="form-check-input" type="checkbox"
                                                                        name="is_active" value="1"
                                                                        {{ $template->is_active ? 'checked' : '' }}><label
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
                                            <td colspan="6" class="text-center py-4 text-muted"><i
                                                    class="ti ti-receipt-off fs-24 d-block mb-2"></i>No templates yet.
                                                Create your first one.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($templates->hasPages())
                        <div class="card-footer">{{ $templates->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
