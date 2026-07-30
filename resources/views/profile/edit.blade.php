@extends('layouts.admin_master')
@section('title', 'My Profile')
@section('content')

    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-user-circle me-2 text-primary"></i>My Profile</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Profile</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ti ti-circle-check me-2"></i>Profile updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('status') === 'password-updated')
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ti ti-circle-check me-2"></i>Password updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- ── Left: Profile Info ─────────────────────────────────────── --}}
            <div class="col-lg-4 mb-4">
                <div class="card text-center">
                    <div class="card-body py-4">
                        <div class="avatar-lg mx-auto mb-3 bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center"
                            style="width:80px;height:80px;">
                            <span class="text-primary fw-bold" style="font-size:2rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        </div>
                        <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                        <p class="text-muted mb-2">{{ auth()->user()->email }}</p>
                        @if (auth()->user()->roles->isNotEmpty())
                            <span class="badge bg-primary-subtle text-primary">
                                {{ auth()->user()->roles->first()->name }}
                            </span>
                        @endif
                        <hr>
                        <div class="text-start">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Member Since</span>
                                <span class="small fw-semibold">{{ auth()->user()->created_at->format('d M Y') }}</span>
                            </div>
                            @if (auth()->user()->company)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Company</span>
                                    <span class="small fw-semibold">{{ auth()->user()->company->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Right: Forms ───────────────────────────────────────────── --}}
            <div class="col-lg-8">

                {{-- Update Profile Information --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ti ti-user-edit me-2"></i>Update Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Full Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="name" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email Address <span
                                        class="text-danger">*</span></label>
                                <input type="email" id="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                    <div class="alert alert-warning mt-2 py-2 small">
                                        <i class="ti ti-alert-triangle me-1"></i>
                                        Your email address is unverified.
                                        <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-link btn-sm p-0 ms-1">
                                                Click here to re-send the verification email.
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>Save Changes
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Update Password --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ti ti-lock me-2"></i>Update Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label fw-semibold">Current Password</label>
                                <input type="password" id="current_password" name="current_password"
                                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                    autocomplete="current-password">
                                @error('current_password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">New Password</label>
                                <input type="password" id="password" name="password"
                                    class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                    autocomplete="new-password">
                                @error('password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirm New
                                    Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-control" autocomplete="new-password">
                            </div>

                            <button type="submit" class="btn btn-warning">
                                <i class="ti ti-lock-check me-1"></i>Update Password
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Delete Account --}}
                <div class="card border-danger">
                    <div class="card-header bg-danger-subtle">
                        <h5 class="card-title mb-0 text-danger"><i class="ti ti-trash me-2"></i>Delete Account</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            Once your account is deleted, all of its resources and data will be permanently deleted.
                            Before deleting your account, please download any data or information that you wish to retain.
                        </p>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                            data-bs-target="#deleteAccountModal">
                            <i class="ti ti-trash me-1"></i>Delete My Account
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Delete Account Confirmation Modal --}}
    <div class="modal fade" id="deleteAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="ti ti-alert-triangle me-2"></i>Delete Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="text-muted">Are you sure you want to delete your account? This action cannot be undone.
                            Please enter your password to confirm.</p>
                        <div class="mb-3">
                            <label for="delete_password" class="form-label fw-semibold">Password</label>
                            <input type="password" id="delete_password" name="password"
                                class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                placeholder="Enter your password" required>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="ti ti-trash me-1"></i>Yes, Delete My Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->hasBag('userDeletion'))
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    new bootstrap.Modal(document.getElementById('deleteAccountModal')).show();
                });
            </script>
        @endpush
    @endif

@endsection
