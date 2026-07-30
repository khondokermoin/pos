@extends('layouts.admin_master')
@section('title', 'Employees')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-id-badge me-2 text-primary"></i>Employee List</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Employees</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-6 text-sm-end">
                <a href="{{ route('company.employees.increments') }}" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="ti ti-trending-up me-1"></i> Increments
                </a>
                <a href="{{ route('company.employees.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Add Employee
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
                @if ($employees->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-id-badge d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                        <h5 class="fw-semibold">No Employees Yet</h5>
                        <p class="small mb-3">Add your first employee to get started with HR management.</p>
                        <a href="{{ route('company.employees.create') }}" class="btn btn-sm btn-primary">
                            <i class="ti ti-plus me-1"></i> Add Employee
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Phone</th>
                                    <th>Join Date</th>
                                    <th class="text-end">Salary</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $emp)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $emp->name }}</strong><br>
                                            <small class="text-muted">{{ $emp->email ?? '' }}</small>
                                        </td>
                                        <td>{{ $emp->department->name ?? '—' }}</td>
                                        <td>{{ $emp->designation ?? '—' }}</td>
                                        <td>{{ $emp->phone ?? '—' }}</td>
                                        <td>{{ $emp->join_date ?? '—' }}</td>
                                        <td class="text-end">{{ number_format($emp->salary, 2) }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('company.employees.edit', $emp->id) }}"
                                                class="btn btn-sm btn-outline-warning me-1">
                                                <i class="ti ti-pencil"></i>
                                            </a>
                                            <form action="{{ route('company.employees.destroy', $emp->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this employee?')">
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
