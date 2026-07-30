@extends('layouts.admin_master')

@section('title', 'Company Profile')

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-building me-2 text-primary"></i>Company Profile</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Company Profile</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Left: Logo & Quick Info --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mx-auto mb-3 rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center overflow-hidden"
                        style="width:100px;height:100px;">
                        @if ($company->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}"
                                style="width:100px;height:100px;object-fit:cover;">
                        @else
                            <span class="text-primary fw-bold" style="font-size:2.5rem;">
                                {{ strtoupper(substr($company->name, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    <h4 class="mb-1">{{ $company->name }}</h4>
                    <p class="text-muted small mb-0">{{ $company->email ?? 'No email set' }}</p>
                    <p class="text-muted small">{{ $company->phone ?? 'No phone set' }}</p>

                    <div class="mt-3">
                        @if ($company->subscription)
                            <span class="badge bg-success fs-6 px-3">
                                <i class="ti ti-crown me-1"></i>{{ $company->subscription->plan->name ?? 'Active Plan' }}
                            </span>
                        @else
                            <span class="badge bg-warning fs-6 px-3">No Active Plan</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Edit Form --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-edit me-2"></i>Edit Company Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('company.settings.profile.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Company Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $company->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $company->email) }}" placeholder="company@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $company->phone) }}" placeholder="e.g., 01700000000">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Address</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2"
                                    placeholder="Full company address">{{ old('address', $company->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">City</label>
                                <input type="text" name="city"
                                    class="form-control @error('city') is-invalid @enderror"
                                    value="{{ old('city', $company->city) }}" placeholder="e.g., Dhaka">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Country</label>
                                <input type="text" name="country"
                                    class="form-control @error('country') is-invalid @enderror"
                                    value="{{ old('country', $company->country ?? 'Bangladesh') }}"
                                    placeholder="e.g., Bangladesh">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Company Logo</label>
                                <input type="file" name="logo"
                                    class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if ($company->logo)
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Current Logo"
                                            class="rounded" style="height:40px;">
                                        <small class="text-muted">Current logo — upload a new one to replace it.</small>
                                    </div>
                                @else
                                    <small class="text-muted">Upload a PNG, JPG or SVG (max 2MB).</small>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ti ti-device-floppy me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
