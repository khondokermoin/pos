@extends('layouts.admin_master')
@section('title', 'Salary Increments')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-trending-up me-2 text-primary"></i>Salary Increments</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('company.employees.index') }}">Employees</a></li>
                        <li class="breadcrumb-item active">Increments</li>
                    </ol>
                </nav>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Record Increment</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('company.employees.increments.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select" required>
                                    <option value="">— Select Employee —</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Increment Amount <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="amount" class="form-control" min="0.01" step="0.01"
                                    value="{{ old('amount') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Effective Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="effective_date" class="form-control"
                                    value="{{ old('effective_date', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Reason</label>
                                <textarea name="reason" class="form-control" rows="2" placeholder="e.g., Annual review">{{ old('reason') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-device-floppy me-1"></i> Record Increment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Increment History</h5>
                    </div>
                    <div class="card-body">
                        @if ($increments->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="ti ti-trending-up d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                                <h5 class="fw-semibold">No Increments Yet</h5>
                                <p class="small">Record salary increments for employees using the form on the left.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Employee</th>
                                            <th>Amount</th>
                                            <th>Effective Date</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($increments as $inc)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $inc->employee->name ?? '—' }}</td>
                                                <td>{{ number_format($inc->amount, 2) }}</td>
                                                <td>{{ $inc->effective_date ?? '—' }}</td>
                                                <td>{{ $inc->reason ?? '—' }}</td>
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
