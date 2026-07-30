@extends('layouts.admin_master')

@section('title', 'Manage Branches')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-6">
                <h4 class="page-title">All Branches</h4>
                <p class="text-muted mb-0">Manage all branches under your company.</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('company.branches.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Add New Branch
                </a>
            </div>
        </div>

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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Branch Name</th>
                                        <th>Address</th>
                                        <th>Phone</th>
                                        <th>Manager</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($branches as $branch)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $branch->name }}</strong>
                                            </td>
                                            <td>{{ $branch->address }}</td>
                                            <td>{{ $branch->phone ?? '—' }}</td>
                                            <td>
                                                @if ($branch->manager)
                                                    <span class="d-flex align-items-center gap-2">
                                                        <span
                                                            class="avatar avatar-xs rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                            style="width:28px;height:28px;font-size:12px;">
                                                            {{ strtoupper(substr($branch->manager->name, 0, 1)) }}
                                                        </span>
                                                        {{ $branch->manager->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Not Assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $branch->status === 'active' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($branch->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                {{-- ✅ 1-Click Login to Branch (Impersonation) --}}
                                                <form action="{{ route('company.branches.impersonate', $branch->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success me-1"
                                                        title="Login to this branch as its Manager"
                                                        onclick="return confirm('You will be logged in as the Manager of \'{{ addslashes($branch->name) }}\'. Continue?')">
                                                        <i class="ti ti-login"></i> Login
                                                    </button>
                                                </form>

                                                {{-- Edit Branch --}}
                                                <a href="{{ route('company.branches.edit', $branch->id) }}"
                                                    class="btn btn-sm btn-warning me-1" title="Edit Branch">
                                                    <i class="ti ti-pencil"></i> Edit
                                                </a>

                                                {{-- Delete Branch --}}
                                                <form action="{{ route('company.branches.destroy', $branch->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete the branch: {{ addslashes($branch->name) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        title="Delete Branch">
                                                        <i class="ti ti-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="ti ti-building-store d-block mb-3"
                                                    style="font-size: 3rem; opacity: 0.4;"></i>
                                                <h5 class="fw-semibold">No Branches Found</h5>
                                                <p class="mb-3 small">You haven't created any branches yet.</p>
                                                <a href="{{ route('company.branches.create') }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus me-1"></i> Add Your First Branch
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- ✅ HOTFIX: Pagination was missing --}}
                        @if ($branches->hasPages())
                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Showing {{ $branches->firstItem() }} to {{ $branches->lastItem() }} of
                                    {{ $branches->total() }} branches
                                </div>
                                {{ $branches->links() }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
