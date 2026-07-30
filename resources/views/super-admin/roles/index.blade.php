@extends('layouts.admin_master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">Roles & Permissions</h4>
                <a href="{{ route('superadmin.roles.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Create New Role
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Role Name</th>
                            <th width="40%">Assigned Permissions</th>
                            <th width="15%">Created At</th>
                            <th width="15%" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>
                                <span class="fw-bold text-primary">{{ $role->name }}</span>
                                @if(in_array($role->name, ['Super Admin', 'Company Admin']))
                                    <span class="badge bg-warning text-dark ms-1">System</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($role->permissions->take(4) as $permission)
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                            {{ str_replace('-', ' ', $permission->name) }}
                                        </span>
                                    @endforeach
                                    @if($role->permissions->count() > 4)
                                        <span class="badge bg-light text-dark border">+{{ $role->permissions->count() - 4 }} more</span>
                                    @endif
                                    @if($role->permissions->count() === 0)
                                        <span class="text-muted fst-italic">No permissions</span>
                                    @endif
                                </div>
                            </td>
                            <td><small class="text-muted">{{ $role->created_at->format('d M, Y') }}</small></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('superadmin.roles.edit', $role->id) }}" class="btn btn-sm btn-info text-white" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    @if(!in_array($role->name, ['Super Admin', 'Company Admin']))
                                    <form action="{{ route('superadmin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role? This will remove these permissions from all users assigned to this role.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No roles found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Showing {{ $roles->firstItem() ?? 0 }} to {{ $roles->lastItem() ?? 0 }} of {{ $roles->total() }}
                </div>
                {{ $roles->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection