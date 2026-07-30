@extends('layouts.admin_master')
@section('title', 'Departments')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-building me-2 text-primary"></i>Departments</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Departments</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-6 text-sm-end">
                <a href="{{ route('company.departments.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Add Department
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                @if ($departments->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-building d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                        <h5 class="fw-semibold">No Departments Yet</h5>
                        <p class="small mb-3">Create departments to organize your employees.</p>
                        <a href="{{ route('company.departments.create') }}" class="btn btn-sm btn-primary">
                            <i class="ti ti-plus me-1"></i> Add Department
                        </a>
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
                                @foreach ($departments as $dept)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $dept->name }}</strong></td>
                                        <td>{{ $dept->description ?? '—' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('company.departments.edit', $dept->id) }}"
                                                class="btn btn-sm btn-outline-warning me-1">
                                                <i class="ti ti-pencil"></i>
                                            </a>
                                            <form action="{{ route('company.departments.destroy', $dept->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this department?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="ti ti-trash"></i>
                                                </button>
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
@endsection
