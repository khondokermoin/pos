@extends('layouts.admin_master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">{{ isset($plan) ? 'Edit Plan' : 'Create New Pricing Plan' }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form
                            action="{{ isset($plan) ? route('superadmin.plans.update', $plan->id) : route('superadmin.plans.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($plan))
                                @method('PUT')
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Plan Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $plan->name ?? '') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status"
                                        name="status" required data-choices data-choices-search-false>
                                        <option value="active"
                                            {{ old('status', $plan->status ?? 'active') == 'active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="inactive"
                                            {{ old('status', $plan->status ?? 'active') == 'inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                        <option value="draft"
                                            {{ old('status', $plan->status ?? 'active') == 'draft' ? 'selected' : '' }}>
                                            Draft</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="price" class="form-label">Price (per month) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('price') is-invalid @enderror" id="price"
                                        name="price" value="{{ old('price', $plan->price ?? '') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="trial_days" class="form-label">Trial Days</label>
                                    <input type="number" class="form-control @error('trial_days') is-invalid @enderror"
                                        id="trial_days" name="trial_days"
                                        value="{{ old('trial_days', $plan->trial_days ?? 0) }}">
                                    <small class="text-muted">0 = No trial</small>
                                </div>
                                <div class="col-md-4">
                                    <label for="billing_cycle" class="form-label">Billing Cycle</label>
                                    <select class="form-select" name="billing_cycle" data-choices data-choices-search-false>
                                        <option value="monthly"
                                            {{ old('billing_cycle', $plan->billing_cycle ?? 'monthly') == 'monthly' ? 'selected' : '' }}>
                                            Monthly</option>
                                        <option value="yearly"
                                            {{ old('billing_cycle', $plan->billing_cycle ?? '') == 'yearly' ? 'selected' : '' }}>
                                            Yearly</option>
                                        <option value="lifetime"
                                            {{ old('billing_cycle', $plan->billing_cycle ?? '') == 'lifetime' ? 'selected' : '' }}>
                                            Lifetime</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="user_limit" class="form-label">User Limit <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('user_limit') is-invalid @enderror"
                                        id="user_limit" name="user_limit"
                                        value="{{ old('user_limit', $plan->user_limit ?? 1) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="branch_limit" class="form-label">Branch Limit <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('branch_limit') is-invalid @enderror"
                                        id="branch_limit" name="branch_limit"
                                        value="{{ old('branch_limit', $plan->branch_limit ?? 1) }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="features" class="form-label">Features (One per line)</label>
                                {{--
                                FIX: The Plan model casts 'features' as 'array' automatically.
                                So $plan->features is already a PHP array — NOT a JSON string.
                                We must NOT call json_decode() on it again (that causes double-decode = null).
                                Simply implode the array directly.
                            --}}
                                <textarea class="form-control @error('features') is-invalid @enderror" id="features" name="features" rows="5"
                                    placeholder="Unlimited POS&#10;24/7 Support&#10;Cloud Backup">{{ old('features', isset($plan) ? (is_array($plan->features) ? implode("\n", $plan->features) : '') : '') }}</textarea>
                                @error('features')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Enter each feature on a new line. They will be shown as bullet
                                    points on the pricing page.</small>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('superadmin.plans.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($plan) ? 'Update Plan' : 'Save Plan' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
