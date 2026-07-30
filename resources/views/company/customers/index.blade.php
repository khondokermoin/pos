@extends('layouts.admin_master')

@section('title', 'Customers')

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-address-book me-2 text-primary"></i>Customers</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Customers</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customerModal"
                onclick="openAddModal()">
                <i class="ti ti-plus me-1"></i> Add Customer
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
                        <h6 class="text-white-50 mb-1">Total Customers</h6>
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
                        <h6 class="text-white-50 mb-1">New This Month</h6>
                        <h2 class="mb-0">{{ $newThisMonth }}</h2>
                    </div>
                    <i class="ti ti-user-plus fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Showing</h6>
                        <h2 class="mb-0">{{ $customers->total() }}</h2>
                        <small class="text-white-50">{{ request('search') ? 'search results' : 'all customers' }}</small>
                    </div>
                    <i class="ti ti-filter fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Search + Add --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('company.customers.index') }}" class="row g-2 align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="ti ti-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0"
                            placeholder="Search by name, phone or email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-filter me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('company.customers.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="ti ti-x me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Customers Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Notes</th>
                            <th>Joined</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td class="ps-3">{{ $customers->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                            style="width:36px;height:36px;min-width:36px;">
                                            <span class="text-primary fw-bold small">
                                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <strong>{{ $customer->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $customer->phone ?? '—' }}</td>
                                <td>{{ $customer->email ?? '—' }}</td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width:130px;"
                                        title="{{ $customer->address }}">
                                        {{ $customer->address ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block text-muted small" style="max-width:100px;"
                                        title="{{ $customer->notes }}">
                                        {{ $customer->notes ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $customer->created_at->format('d M Y') }}</small>
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-soft-warning me-1" title="Edit Customer"
                                        onclick="openEditModal(
                                            {{ $customer->id }},
                                            '{{ addslashes($customer->name) }}',
                                            '{{ addslashes($customer->email ?? '') }}',
                                            '{{ addslashes($customer->phone ?? '') }}',
                                            '{{ addslashes($customer->address ?? '') }}',
                                            '{{ addslashes($customer->notes ?? '') }}'
                                        )">
                                        <i class="ti ti-pencil"></i>
                                    </button>
                                    <form action="{{ route('company.customers.destroy', $customer->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete customer: {{ addslashes($customer->name) }}?')">
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
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="ti ti-address-book-off d-block mb-3"
                                        style="font-size:3rem;opacity:0.4;"></i>
                                    <h5 class="fw-semibold">No Customers Found</h5>
                                    @if (request('search'))
                                        <p class="small mb-2">No results for "<strong>{{ request('search') }}</strong>"
                                        </p>
                                        <a href="{{ route('company.customers.index') }}"
                                            class="btn btn-sm btn-outline-secondary">Clear Search</a>
                                    @else
                                        <p class="small mb-3">You haven't added any customers yet.</p>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#customerModal" onclick="openAddModal()">
                                            <i class="ti ti-plus me-1"></i> Add First Customer
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($customers->hasPages())
                <div class="px-3 py-2 d-flex justify-content-between align-items-center border-top">
                    <div class="text-muted small">
                        Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }}
                        of {{ $customers->total() }} customers
                    </div>
                    {{ $customers->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Add / Edit Customer Modal --}}
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="customerForm" method="POST" action="{{ route('company.customers.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="modal-header">
                        <h5 class="modal-title" id="customerModalLabel">
                            <i class="ti ti-user-plus me-2"></i>Add New Customer
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="customerName" class="form-control"
                                    placeholder="e.g., Md. Karim" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" name="phone" id="customerPhone" class="form-control"
                                    placeholder="e.g., 01700000000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" id="customerEmail" class="form-control"
                                    placeholder="e.g., customer@email.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Address</label>
                                <input type="text" name="address" id="customerAddress" class="form-control"
                                    placeholder="Full address">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea name="notes" id="customerNotes" class="form-control" rows="2"
                                    placeholder="Any additional notes..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="ti ti-device-floppy me-1"></i> Save Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const storeUrl = "{{ route('company.customers.store') }}";

        function openAddModal() {
            document.getElementById('customerModalLabel').innerHTML =
                '<i class="ti ti-user-plus me-2"></i>Add New Customer';
            document.getElementById('customerForm').action = storeUrl;
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('customerName').value = '';
            document.getElementById('customerPhone').value = '';
            document.getElementById('customerEmail').value = '';
            document.getElementById('customerAddress').value = '';
            document.getElementById('customerNotes').value = '';
            document.getElementById('submitBtn').innerHTML =
                '<i class="ti ti-device-floppy me-1"></i> Save Customer';
        }

        function openEditModal(id, name, email, phone, address, notes) {
            const updateUrl = "{{ url('company/customers') }}/" + id;
            document.getElementById('customerModalLabel').innerHTML =
                '<i class="ti ti-pencil me-2"></i>Edit Customer';
            document.getElementById('customerForm').action = updateUrl;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('customerName').value = name;
            document.getElementById('customerPhone').value = phone;
            document.getElementById('customerEmail').value = email;
            document.getElementById('customerAddress').value = address;
            document.getElementById('customerNotes').value = notes;
            document.getElementById('submitBtn').innerHTML =
                '<i class="ti ti-device-floppy me-1"></i> Update Customer';

            const modal = new bootstrap.Modal(document.getElementById('customerModal'));
            modal.show();
        }
    </script>
@endpush
