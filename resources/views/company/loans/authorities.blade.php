@extends('layouts.admin_master')
@section('title', 'Loan Authorities')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-building-bank me-2 text-primary"></i>Loan Authorities</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Loan Authorities</li>
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
                        <h5 class="mb-0">Add Authority</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('company.loans.authorities.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="e.g., BRAC Bank" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Contact</label>
                                <input type="text" name="contact" class="form-control" value="{{ old('contact') }}"
                                    placeholder="Phone or email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="ti ti-plus me-1"></i> Add
                                Authority</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All Authorities</h5>
                    </div>
                    <div class="card-body">
                        @if ($authorities->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="ti ti-building-bank d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                                <h5 class="fw-semibold">No Authorities Yet</h5>
                                <p class="small">Add banks or lenders that provide loans to your company.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Contact</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($authorities as $auth)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><strong>{{ $auth->name }}</strong></td>
                                                <td>{{ $auth->contact ?? '—' }}</td>
                                                <td>{{ $auth->notes ?? '—' }}</td>
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
