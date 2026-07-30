@extends('layouts.admin_master')

@section('title', 'Add New Category')

@section('content')
    <div class="mb-3 row align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0">
                <i class="ti ti-plus me-2 text-primary"></i> Add New Category
            </h4>
        </div>
        <div class="col-sm-6 text-sm-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-end mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('company.categories.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active">Add Category</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('company.categories.store') }}" method="POST" autocomplete="off">
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="name" class="form-label fw-semibold">
                            Category Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               placeholder="e.g., Electronics, Groceries, Clothing" 
                               required 
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">
                                <i class="ti ti-alert-circle me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 mt-4 pt-2 border-top">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('company.categories.index') }}" class="btn btn-light border">
                                <i class="ti ti-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ti ti-device-floppy me-1"></i> Save Category
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection