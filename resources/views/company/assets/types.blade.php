@extends('layouts.admin_master')
@section('title', 'Asset Types')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-category me-2 text-primary"></i>Asset Types</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Asset Types</li>
                    </ol>
                </nav>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show"><i
                    class="ti ti-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row g-3">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Add Asset Type</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('company.assets.types.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Type Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="e.g., Vehicle, Machinery" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="ti ti-plus me-1"></i> Add
                                Type</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">All Asset Types</h5>
                        <a href="{{ route('company.assets.assets') }}" class="btn btn-sm btn-outline-primary">
                            <i class="ti ti-building-warehouse me-1"></i> View Assets
                        </a>
                    </div>
                    <div class="card-body">
                        @if ($types->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="ti ti-category d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                                <h5 class="fw-semibold">No Asset Types Yet</h5>
                                <p class="small">Add categories like Vehicle, Machinery, Furniture, etc.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($types as $type)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><strong>{{ $type->name }}</strong></td>
                                                <td>{{ $type->description ?? '—' }}</td>
                                                <td class="text-end">
                                                    <form action="{{ route('company.assets.types.destroy', $type->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Delete this type?')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger"><i
                                                                class="ti ti-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
