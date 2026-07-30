@extends('layouts.admin_master')

@section('title', isset($branch) ? 'Edit Branch' : 'Create Branch')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-6">
                <h4 class="page-title">
                    {{ isset($branch) ? 'Edit Branch: ' . $branch->name : 'Add New Branch' }}
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('company.branches.index') }}">Branches</a></li>
                        <li class="breadcrumb-item active">{{ isset($branch) ? 'Edit' : 'Create' }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('company.branches.index') }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>

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
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="ti ti-building-store me-2"></i>Branch Information
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (isset($branch))
                            <form action="{{ route('company.branches.update', $branch->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                            @else
                                <form action="{{ route('company.branches.store') }}" method="POST">
                                    @csrf
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Branch Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $branch->name ?? '') }}" placeholder="e.g., Dhaka Main Branch"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Full Address <span
                                        class="text-danger">*</span></label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3"
                                    placeholder="Enter branch address" required>{{ old('address', $branch->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="text" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $branch->phone ?? '') }}" placeholder="e.g., 01700000000">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Branch Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $branch->email ?? '') }}"
                                    placeholder="e.g., branch@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Assign Branch Manager (Optional)</label>
                                <select name="manager_id" class="form-select @error('manager_id') is-invalid @enderror">
                                    <option value="">-- Select a Staff Member --</option>
                                    @foreach ($managers as $manager)
                                        <option value="{{ $manager->id }}"
                                            {{ old('manager_id', $branch->manager_id ?? '') == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }} ({{ $manager->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('manager_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">You can change the manager at any time.</small>
                            </div>

                            @if (isset($branch))
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Branch Status <span
                                            class="text-danger">*</span></label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror"
                                        required>
                                        <option value="active"
                                            {{ old('status', $branch->status) === 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="inactive"
                                            {{ old('status', $branch->status) === 'inactive' ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Inactive branches won't appear in POS or transfers.</small>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('company.branches.index') }}" class="btn btn-light">
                                <i class="ti ti-x me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ti ti-device-floppy me-1"></i>
                                {{ isset($branch) ? 'Update Branch' : 'Create Branch' }}
                            </button>
                        </div>

                        </form>
                    </div>
                </div>

                @if (isset($branch))
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="text-muted small text-uppercase fw-semibold mb-3">Branch Meta</h6>
                            <div class="row g-2 text-muted small">
                                <div class="col-6">
                                    <span class="d-block">Created</span>
                                    <strong class="text-body">{{ $branch->created_at->format('d M Y, h:i A') }}</strong>
                                </div>
                                <div class="col-6">
                                    <span class="d-block">Last Updated</span>
                                    <strong class="text-body">{{ $branch->updated_at->format('d M Y, h:i A') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
