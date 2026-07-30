@extends('layouts.super-admin')
@section('title', 'Barcode Settings')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Barcode Settings</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Barcode Settings</li>
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
                        <h5 class="card-title mb-0"><i class="ti ti-plus me-2"></i>Add Barcode Setting</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('superadmin.barcode-settings.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Setting Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="e.g. Standard Label" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Barcode Type</label>
                                <select name="barcode_type" class="form-select">
                                    @foreach (['CODE128', 'CODE39', 'EAN13', 'QR'] as $type)
                                        <option value="{{ $type }}"
                                            {{ old('barcode_type', 'CODE128') == $type ? 'selected' : '' }}>{{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Width (px)</label>
                                    <input type="number" name="width" class="form-control" value="{{ old('width', 2) }}"
                                        min="1" max="10">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Height (px)</label>
                                    <input type="number" name="height" class="form-control"
                                        value="{{ old('height', 50) }}" min="20" max="200">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Labels Per Row</label>
                                <input type="number" name="labels_per_row" class="form-control"
                                    value="{{ old('labels_per_row', 3) }}" min="1" max="6">
                            </div>
                            <div class="mb-3">
                                <label class="form-label d-block">Display Options</label>
                                @foreach (['show_text' => 'Show Barcode Text', 'show_price' => 'Show Price', 'show_product_name' => 'Show Product Name', 'show_company_name' => 'Show Company Name'] as $field => $label)
                                    <div class="form-check form-switch mb-1">
                                        <input class="form-check-input" type="checkbox" name="{{ $field }}"
                                            value="1" {{ old($field, 1) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex gap-3 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_default" value="1"
                                        {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="form-check-label">Set as Default</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                    <label class="form-check-label">Active</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="ti ti-device-floppy me-1"></i>
                                Save Setting</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Settings List -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ti ti-barcode me-2 text-primary"></i>All Barcode Settings
                            <span class="badge bg-secondary ms-2">{{ $settings->total() }}</span></h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Labels/Row</th>
                                        <th>Default</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($settings as $setting)
                                        <tr>
                                            <td><strong>{{ $setting->name }}</strong></td>
                                            <td><span
                                                    class="badge bg-primary-subtle text-primary">{{ $setting->barcode_type }}</span>
                                            </td>
                                            <td><small>{{ $setting->width }}×{{ $setting->height }}px</small></td>
                                            <td>{{ $setting->labels_per_row }}</td>
                                            <td>
                                                @if ($setting->is_default)
                                                    <span class="badge bg-success"><i
                                                            class="ti ti-star-filled me-1"></i>Default</span>
                                                @else
                                                    <form method="POST"
                                                        action="{{ route('superadmin.barcode-settings.set-default', $setting) }}"
                                                        class="d-inline">
                                                        @csrf @method('PATCH')
                                                        <button type="submit"
                                                            class="btn btn-xs btn-outline-secondary">Set Default</button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($setting->is_active)
                                                    <span class="badge bg-success-subtle text-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-warning me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editBarcode{{ $setting->id }}">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <form method="POST"
                                                    action="{{ route('superadmin.barcode-settings.destroy', $setting) }}"
                                                    class="d-inline" onsubmit="return confirm('Delete this setting?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i
                                                            class="ti ti-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editBarcode{{ $setting->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit: {{ $setting->name }}</h5><button
                                                            type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST"
                                                        action="{{ route('superadmin.barcode-settings.update', $setting) }}">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3"><label
                                                                    class="form-label">Name</label><input type="text"
                                                                    name="name" class="form-control"
                                                                    value="{{ $setting->name }}" required></div>
                                                            <div class="mb-3"><label class="form-label">Barcode
                                                                    Type</label>
                                                                <select name="barcode_type" class="form-select">
                                                                    @foreach (['CODE128', 'CODE39', 'EAN13', 'QR'] as $type)
                                                                        <option value="{{ $type }}"
                                                                            {{ $setting->barcode_type == $type ? 'selected' : '' }}>
                                                                            {{ $type }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-6"><label
                                                                        class="form-label">Width</label><input
                                                                        type="number" name="width"
                                                                        class="form-control"
                                                                        value="{{ $setting->width }}" min="1"
                                                                        max="10"></div>
                                                                <div class="col-6"><label
                                                                        class="form-label">Height</label><input
                                                                        type="number" name="height"
                                                                        class="form-control"
                                                                        value="{{ $setting->height }}" min="20"
                                                                        max="200"></div>
                                                            </div>
                                                            <div class="mb-3"><label class="form-label">Labels Per
                                                                    Row</label><input type="number" name="labels_per_row"
                                                                    class="form-control"
                                                                    value="{{ $setting->labels_per_row }}" min="1"
                                                                    max="6"></div>
                                                            @foreach (['show_text' => 'Show Text', 'show_price' => 'Show Price', 'show_product_name' => 'Show Product Name', 'show_company_name' => 'Show Company Name'] as $field => $label)
                                                                <div class="form-check form-switch mb-1">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="{{ $field }}" value="1"
                                                                        {{ $setting->$field ? 'checked' : '' }}>
                                                                    <label
                                                                        class="form-check-label">{{ $label }}</label>
                                                                </div>
                                                            @endforeach
                                                            <div class="d-flex gap-3 mt-2">
                                                                <div class="form-check form-switch"><input
                                                                        class="form-check-input" type="checkbox"
                                                                        name="is_default" value="1"
                                                                        {{ $setting->is_default ? 'checked' : '' }}><label
                                                                        class="form-check-label">Default</label></div>
                                                                <div class="form-check form-switch"><input
                                                                        class="form-check-input" type="checkbox"
                                                                        name="is_active" value="1"
                                                                        {{ $setting->is_active ? 'checked' : '' }}><label
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
                                            <td colspan="7" class="text-center py-4 text-muted"><i
                                                    class="ti ti-barcode-off fs-24 d-block mb-2"></i>No barcode settings
                                                yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($settings->hasPages())
                        <div class="card-footer">{{ $settings->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
