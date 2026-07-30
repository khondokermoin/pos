@extends('layouts.admin_master')

@section('title', 'Assign Role — ' . $user->name)

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0">
                    <i class="ti ti-shield-check me-2 text-primary"></i>Assign Role
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('company.users.index') }}">Staff & Roles</a></li>
                        <li class="breadcrumb-item active">Assign Role</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-6 text-sm-end">
                <a href="{{ route('company.users.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Back to Staff List
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

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

        <div class="row">
            <div class="col-lg-6 mx-auto">

                {{-- User Info Card --}}
                <div class="card mb-3">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar avatar-md rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;font-size:20px;flex-shrink:0;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold">{{ $user->name }}</h5>
                            <small class="text-muted">{{ $user->email }}</small><br>
                            <small class="text-muted">
                                Current Role:
                                @if ($user->roles->isNotEmpty())
                                    @foreach ($user->roles as $role)
                                        <span class="badge bg-primary-lt text-primary">{{ $role->name }}</span>
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary-lt text-secondary">No Role</span>
                                @endif
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Assign Role Form --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ti ti-shield me-2"></i>Change Role</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('company.users.assign-role', $user->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Select New Role <span
                                        class="text-danger">*</span></label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="">— Select a Role —</option>
                                    <option value="Manager" {{ $user->hasRole('Manager') ? 'selected' : '' }}>
                                        Manager — Full branch access (inventory, POS, reports)
                                    </option>
                                    <option value="Salesman" {{ $user->hasRole('Salesman') ? 'selected' : '' }}>
                                        Salesman — POS & sales only
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted mt-1 d-block">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Changing the role takes effect immediately on the next login.
                                </small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <a href="{{ route('company.users.index') }}" class="btn btn-light">
                                    <i class="ti ti-x me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-shield-check me-1"></i> Update Role
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
