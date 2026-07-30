@extends('layouts.admin_master')

@section('title', 'Suppliers')

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-truck me-2 text-primary"></i>Suppliers</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Suppliers</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#supplierModal"
                onclick="openAddModal()">
                <i class="ti ti-plus me-1"></i> Add Supplier
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

    {{-- Stats Row --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Suppliers</h6>
                        <h2 class="mb-0">{{ $totalCount }}</h2>
                    </div>
                    <i class="ti ti-users fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Active</h6>
                        <h2 class="mb-0">{{ $activeCount }}</h2>
                    </div>
                    <i class="ti ti-circle-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-secondary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Inactive</h6>
                        <h2 class="mb-0">{{ $inactiveCount }}</h2>
                    </div>
                    <i class="ti ti-circle-x fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Search Bar --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('company.suppliers.index') }}" class="row g-2 align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="ti ti-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0"
                            placeholder="Search by name, phone, email or contact person..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-filter me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('company.suppliers.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="ti ti-x me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Suppliers Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Supplier Name</th>
                            <th>Contact Person</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td class="ps-3">{{ $suppliers->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                            style="width:36px;height:36px;">
                                            <span class="text-primary fw-bold">
                                                {{ strtoupper(substr($supplier->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <strong>{{ $supplier->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $supplier->contact_person ?? '—' }}</td>
                                <td>{{ $supplier->phone ?? '—' }}</td>
                                <td>{{ $supplier->email ?? '—' }}</td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width:150px;"
                                        title="{{ $supplier->address }}">
                                        {{ $supplier->address ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $supplier->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($supplier->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    {{-- Edit Button — opens modal with pre-filled data --}}
                                    <button type="button" class="btn btn-sm btn-soft-warning me-1" title="Edit Supplier"
                                        onclick="openEditModal(
                                            {{ $supplier->id }},
                                            '{{ addslashes($supplier->name) }}',
                                            '{{ addslashes($supplier->email ?? '') }}',
                                            '{{ addslashes($supplier->phone ?? '') }}',
                                            '{{ addslashes($supplier->address ?? '') }}',
                                            '{{ addslashes($supplier->contact_person ?? '') }}',
                                            '{{ addslashes($supplier->notes ?? '') }}',
                                            '{{ $supplier->status }}'
                                        )">
                                        <i class="ti ti-pencil"></i>
                                    </button>

                                    {{-- Delete Button --}}
                                    <form action="{{ route('company.suppliers.destroy', $supplier->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete supplier: {{ addslashes($supplier->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete Supplier">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="ti ti-truck-off d-block mb-3" style="font-size:3rem;opacity:0.4;"></i>
                                    <h5 class="fw-semibold">No Suppliers Found</h5>
                                    @if (request('search'))
                                        <p class="small mb-2">No results for "<strong>{{ request('search') }}</strong>"
                                        </p>
                                        <a href="{{ route('company.suppliers.index') }}"
                                            class="btn btn-sm btn-outline-secondary">Clear Search</a>
                                    @else
                                        <p class="small mb-3">You haven't added any suppliers yet.</p>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#supplierModal" onclick="openAddModal()">
                                            <i class="ti ti-plus me-1"></i> Add First Supplier
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($suppliers->hasPages())
                <div class="px-3 py-2 d-flex justify-content-between align-items-center border-top">
                    <div class="text-muted small">
                        Showing {{ $suppliers->firstItem() }} to {{ $suppliers->lastItem() }}
                        of {{ $suppliers->total() }} suppliers
                    </div>
                    {{ $suppliers->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================
         Add / Edit Supplier Modal
    ============================================================ --}}
    <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="supplierForm" method="POST" action="{{ route('company.suppliers.store') }}">
                    @csrf
                    {{-- Hidden method field — switched by JS for PUT on edit --}}
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="modal-header">
                        <h5 class="modal-title" id="supplierModalLabel">
                            <i class="ti ti-truck me-2"></i>Add New Supplier
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Supplier Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="supplierName" class="form-control"
                                    placeholder="e.g., ABC Trading Co." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact Person</label>
                                <input type="text" name="contact_person" id="supplierContact" class="form-control"
                                    placeholder="e.g., Mr. Karim">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" name="phone" id="supplierPhone" class="form-control"
                                    placeholder="e.g., 01700000000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" id="supplierEmail" class="form-control"
                                    placeholder="e.g., supplier@example.com">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Address</label>
                                <input type="text" name="address" id="supplierAddress" class="form-control"
                                    placeholder="Full address">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea name="notes" id="supplierNotes" class="form-control" rows="2"
                                    placeholder="Any additional notes..."></textarea>
                            </div>
                            <div class="col-md-4" id="statusField" style="display:none;">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" id="supplierStatus" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="ti ti-device-floppy me-1"></i> Save Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const storeUrl = "{{ route('company.suppliers.store') }}";

        /**
         * Open modal in ADD mode
         */
        function openAddModal() {
            document.getElementById('supplierModalLabel').innerHTML =
                '<i class="ti ti-truck me-2"></i>Add New Supplier';
            document.getElementById('supplierForm').action = storeUrl;
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('supplierName').value = '';
            document.getElementById('supplierContact').value = '';
            document.getElementById('supplierPhone').value = '';
            document.getElementById('supplierEmail').value = '';
            document.getElementById('supplierAddress').value = '';
            document.getElementById('supplierNotes').value = '';
            document.getElementById('statusField').style.display = 'none';
            document.getElementById('submitBtn').innerHTML =
                '<i class="ti ti-device-floppy me-1"></i> Save Supplier';
        }

        /**
         * Open modal in EDIT mode — pre-fill all fields
         */
        function openEditModal(id, name, email, phone, address, contact, notes, status) {
            const updateUrl = "{{ url('company/suppliers') }}/" + id;

            document.getElementById('supplierModalLabel').innerHTML =
                '<i class="ti ti-pencil me-2"></i>Edit Supplier';
            document.getElementById('supplierForm').action = updateUrl;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('supplierName').value = name;
            document.getElementById('supplierContact').value = contact;
            document.getElementById('supplierPhone').value = phone;
            document.getElementById('supplierEmail').value = email;
            document.getElementById('supplierAddress').value = address;
            document.getElementById('supplierNotes').value = notes;
            document.getElementById('supplierStatus').value = status;
            document.getElementById('statusField').style.display = 'block';
            document.getElementById('submitBtn').innerHTML =
                '<i class="ti ti-device-floppy me-1"></i> Update Supplier';

            // Open the modal
            const modal = new bootstrap.Modal(document.getElementById('supplierModal'));
            modal.show();
        }
    </script>
@endpush
