@extends('layouts.admin_master')

@section('title', 'Variant & Attribute Settings')

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-tags me-2 text-primary"></i>Variant & Attribute Settings</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Attributes</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#attributeModal"
                onclick="openAddModal()">
                <i class="ti ti-plus me-1"></i> Add Attribute
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Info Banner --}}
    <div class="alert alert-info d-flex align-items-center mb-4">
        <i class="ti ti-info-circle fs-4 me-3"></i>
        <div>
            <strong>What are Attributes?</strong>
            Attributes define the variant options for your products (e.g., <strong>Color</strong>: Red, Blue, Green |
            <strong>Size</strong>: S, M, L, XL). These are used when creating products with variants.
        </div>
    </div>

    {{-- Attributes Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Attribute Name</th>
                            <th>Values</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attributes as $attribute)
                            <tr>
                                <td class="ps-3">{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $attribute->name }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($attribute->values as $value)
                                            <span class="badge bg-light text-dark border">{{ $value->value }}</span>
                                        @empty
                                            <span class="text-muted small">No values defined</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-soft-warning me-1" title="Edit Attribute"
                                        onclick="openEditModal(
                                            {{ $attribute->id }},
                                            '{{ addslashes($attribute->name) }}',
                                            '{{ addslashes($attribute->values->pluck('value')->join(', ')) }}'
                                        )">
                                        <i class="ti ti-pencil"></i>
                                    </button>
                                    <form action="{{ route('company.settings.attributes.destroy', $attribute->id) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete attribute: {{ addslashes($attribute->name) }}? This will also remove all its values.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="ti ti-tags-off d-block mb-3" style="font-size:3rem;opacity:0.4;"></i>
                                    <h5 class="fw-semibold">No Attributes Defined</h5>
                                    <p class="small mb-3">Create attributes like Color, Size, Weight to use as product
                                        variants.</p>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#attributeModal" onclick="openAddModal()">
                                        <i class="ti ti-plus me-1"></i> Add First Attribute
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add / Edit Attribute Modal --}}
    <div class="modal fade" id="attributeModal" tabindex="-1" aria-labelledby="attributeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="attributeForm" method="POST" action="{{ route('company.settings.attributes.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="modal-header">
                        <h5 class="modal-title" id="attributeModalLabel">
                            <i class="ti ti-tag me-2"></i>Add New Attribute
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Attribute Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="attributeName" class="form-control"
                                placeholder="e.g., Color, Size, Weight, Material" required>
                            <small class="text-muted">This is the attribute group name.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Values</label>
                            <textarea name="values" id="attributeValues" class="form-control" rows="4"
                                placeholder="Enter values separated by commas or new lines:&#10;Red, Blue, Green&#10;or&#10;Red&#10;Blue&#10;Green"></textarea>
                            <small class="text-muted">
                                <i class="ti ti-info-circle me-1"></i>
                                Separate values with commas or new lines. e.g., <code>Red, Blue, Green</code>
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="ti ti-device-floppy me-1"></i> Save Attribute
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const storeUrl = "{{ route('company.settings.attributes.store') }}";

        function openAddModal() {
            document.getElementById('attributeModalLabel').innerHTML =
                '<i class="ti ti-tag me-2"></i>Add New Attribute';
            document.getElementById('attributeForm').action = storeUrl;
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('attributeName').value = '';
            document.getElementById('attributeValues').value = '';
            document.getElementById('submitBtn').innerHTML =
                '<i class="ti ti-device-floppy me-1"></i> Save Attribute';
        }

        function openEditModal(id, name, values) {
            const updateUrl = "{{ url('company/settings/attributes') }}/" + id;
            document.getElementById('attributeModalLabel').innerHTML =
                '<i class="ti ti-pencil me-2"></i>Edit Attribute';
            document.getElementById('attributeForm').action = updateUrl;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('attributeName').value = name;
            document.getElementById('attributeValues').value = values;
            document.getElementById('submitBtn').innerHTML =
                '<i class="ti ti-device-floppy me-1"></i> Update Attribute';

            const modal = new bootstrap.Modal(document.getElementById('attributeModal'));
            modal.show();
        }
    </script>
@endpush
