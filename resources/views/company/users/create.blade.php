@extends('layouts.admin_master')

@section('title', 'Add New Staff Member')

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-user-plus me-2 text-primary"></i>Add New Staff Member</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('company.users.index') }}">Staff & Roles</a></li>
                        <li class="breadcrumb-item active">Add Staff</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-6 text-sm-end">
                <a href="{{ route('company.users.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Back to Staff List
                </a>
            </div>
        </div>

        {{-- User Limit Banner --}}
        @if (isset($userLimit))
            <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                <i class="ti ti-users me-2 fs-5"></i>
                <div>
                    Staff Usage: <strong>{{ $currentStaffCount }} / {{ $userLimit }}</strong> members used.
                    @if ($userLimit - $currentStaffCount <= 2)
                        <span class="text-warning fw-semibold ms-2">⚠ Only {{ $userLimit - $currentStaffCount }} slot(s)
                            remaining!</span>
                    @endif
                </div>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-circle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('company.users.store') }}" method="POST">
            @csrf
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0"><i class="ti ti-id-badge me-2 text-primary"></i>Staff Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        {{-- Full Name --}}
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Full Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" placeholder="e.g. Rahim Uddin" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email Address <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" placeholder="e.g. rahim@example.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">Password <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="Minimum 6 characters" required>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePassword('password', this)">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirm Password <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="Re-enter password" required>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePassword('password_confirmation', this)">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Role --}}
                        {{--
                        ⚠️ CRITICAL: Role names here MUST exactly match the Spatie roles in the DB
                        AND the middleware guard: role:Manager|Salesman
                        - 'Manager'  → can log into branch panel, manage inventory, POS
                        - 'Salesman' → can log into branch panel, use POS only
                        DO NOT use 'Branch Manager' or 'Cashier' — those don't match the middleware!
                    --}}
                        <div class="col-md-6">
                            <label for="role" class="form-label fw-semibold">Role <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role"
                                required>
                                <option value="">— Select a Role —</option>
                                <option value="Manager" {{ old('role') == 'Manager' ? 'selected' : '' }}>
                                    Manager (Full branch access — inventory, POS, reports)
                                </option>
                                <option value="Salesman" {{ old('role') == 'Salesman' ? 'selected' : '' }}>
                                    Salesman (POS & sales only)
                                </option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="ti ti-info-circle me-1"></i>
                                Staff members log into the <strong>Branch Panel</strong> using their email & password.
                            </small>
                        </div>

                        {{-- Branch Assignment --}}
                        <div class="col-md-6">
                            <label for="branch_id" class="form-label fw-semibold">Assign to Branch</label>
                            <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id"
                                name="branch_id">
                                <option value="">— No specific branch —</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                        @if ($branch->status === 'inactive')
                                            (Inactive)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="ti ti-info-circle me-1"></i>
                                Assign a branch so the staff member can only see that branch's data.
                            </small>
                        </div>

                    </div>{{-- end .row --}}
                </div>
                <div class="card-footer bg-white d-flex justify-content-end gap-2">
                    <a href="{{ route('company.users.index') }}" class="btn btn-light">
                        <i class="ti ti-x me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-user-plus me-1"></i> Create Staff Member
                    </button>
                </div>
            </div>
        </form>

    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword(fieldId, btn) {
            const field = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('ti-eye', 'ti-eye-off');
            } else {
                field.type = 'password';
                icon.classList.replace('ti-eye-off', 'ti-eye');
            }
        }
    </script>
@endpush
