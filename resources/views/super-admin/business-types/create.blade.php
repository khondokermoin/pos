@extends('layouts.admin_master')
@section('title', 'Add Business Type')
@section('content')
    <div class="row mb-2 mt-3">
        <div class="col-sm-6">
            <h4 class="page-title">Add Business Type</h4>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('superadmin.business-types.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('superadmin.business-types.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Business Type Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required placeholder="e.g., Grocery, Pharmacy, Electronics">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Slug (URL Identifier)</label>
                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                        value="{{ old('slug') }}" placeholder="auto-generated-if-empty">
                    <small class="text-muted">Leave empty to auto-generate from name (e.g., 'grocery-store' ->
                        'grocery-store')</small>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
                <button type="submit" class="btn btn-primary">Save Business Type</button>
            </form>
        </div>
    </div>
@endsection
