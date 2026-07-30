@extends('layouts.admin_master')

@section('title', 'Staff & Roles')

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-users me-2 text-primary"></i>Staff & Roles</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Staff & Roles</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('company.users.create') }}" class="btn btn-primary">
                <i class="ti ti-user-plus me-1"></i> Add New Staff
            </a>
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
    @php
        $totalStaff = $users->total();
        $rolesInUse = $users->getCollection()->flatMap(fn($u) => $u->roles->pluck('name'))->unique()->count();
    @endphp
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Staff</h6>
                        <h2 class="mb-0">{{ $totalStaff }}</h2>
                    </div>
                    <i class="ti ti-users fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Branches Covered</h6>
                        <h2 class="mb-0">{{ $branches->count() }}</h2>
                    </div>
                    <i class="ti ti-building-store fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Roles in Use</h6>
                        <h2 class="mb-0">{{ $rolesInUse }}</h2>
                    </div>
                    <i class="ti ti-shield fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Staff Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Staff Member</th>
                            <th>Role</th>
                            <th>Branch</th>
                            <th>Joined</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            @php
                                $roleName = $user->roles->first()?->name ?? 'No Role';
                                $roleColors = [
                                    'Company Admin' => 'danger',
                                    'Branch Manager' => 'warning',
                                    'Cashier' => 'success',
                                    'Salesman' => 'info',
                                    'Manager' => 'primary',
                                ];
                                $roleColor = $roleColors[$roleName] ?? 'secondary';
                                $initials = strtoupper(substr($user->name, 0, 1));
                                $bgColors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger'];
                                $bgColor = $bgColors[$user->id % count($bgColors)];
                            @endphp
                            <tr>
                                <td class="ps-3">{{ $users->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle {{ $bgColor }} text-white d-flex align-items-center justify-content-center fw-bold"
                                            style="width:40px;height:40px;min-width:40px;font-size:1rem;">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $roleColor }}">{{ $roleName }}</span>
                                </td>
                                <td>
                                    @if ($user->branch)
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="ti ti-building-store me-1"></i>{{ $user->branch->name }}
                                        </span>
                                    @else
                                        <span class="text-muted small">All Branches</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $user->created_at->format('d M Y') }}</small>
                                </td>
                                <td class="text-end pe-3">
                                    {{-- Change Role Button --}}
                                    <button type="button" class="btn btn-sm btn-soft-primary me-1" title="Change Role"
                                        onclick="openRoleModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $roleName }}')">
                                        <i class="ti ti-shield me-1"></i> Role
                                    </button>

                                    {{-- Delete Button (prevent self-deletion) --}}
                                    @if ($user->id !== Auth::id())
                                        <form action="{{ route('company.users.destroy', $user->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Remove staff member: {{ addslashes($user->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger" title="Remove">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-light text-muted border">You</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="ti ti-users-off d-block mb-3" style="font-size:3rem;opacity:0.4;"></i>
                                    <h5 class="fw-semibold">No Staff Members Found</h5>
                                    <p class="small mb-3">You haven't added any staff members yet.</p>
                                    <a href="{{ route('company.users.create') }}" class="btn btn-sm btn-primary">
                                        <i class="ti ti-user-plus me-1"></i> Add First Staff Member
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="px-3 py-2 d-flex justify-content-between align-items-center border-top">
                    <div class="text-muted small">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }}
                        of {{ $users->total() }} staff members
                    </div>
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Change Role Modal --}}
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form id="roleForm" method="POST" action="">
                    @csrf
                    @method('PATCH')

                    <div class="modal-header">
                        <h5 class="modal-title" id="roleModalLabel">
                            <i class="ti ti-shield me-2"></i>Change Role
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            Changing role for: <strong id="roleUserName"></strong>
                        </p>
                        {{--
                            ⚠️ CRITICAL: Role names MUST exactly match the Spatie roles in the DB
                            AND the middleware guards:
                            - Branch routes use: role:Manager|Salesman
                            - Company routes use: role:Company Admin
                            DO NOT use 'Branch Manager' or 'Cashier' — those are NOT registered roles
                            and will cause a 403 "no role assigned" error on login.
                        --}}
                        <label class="form-label fw-semibold">Select New Role</label>
                        <select name="role" id="roleSelect" class="form-select" required>
                            <option value="Manager">Manager (Full branch access)</option>
                            <option value="Salesman">Salesman (POS & sales only)</option>
                        </select>
                        <small class="text-muted mt-1 d-block">
                            <i class="ti ti-info-circle me-1"></i>
                            Staff log into the <strong>Branch Panel</strong> with their email & password.
                        </small>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="ti ti-check me-1"></i> Update Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openRoleModal(userId, userName, currentRole) {
            const assignRoleUrl = "{{ url('company/users') }}/" + userId + "/assign-role";
            document.getElementById('roleForm').action = assignRoleUrl;
            document.getElementById('roleUserName').textContent = userName;
            document.getElementById('roleSelect').value = currentRole;

            const modal = new bootstrap.Modal(document.getElementById('roleModal'));
            modal.show();
        }
    </script>
@endpush
