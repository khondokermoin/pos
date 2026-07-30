@extends('layouts.super-admin')
@section('title', 'Tenants / Companies')
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-building-store me-2 text-primary"></i>Tenants</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tenants</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('superadmin.companies.create') }}" class="btn btn-primary btn-sm">
                <i class="ti ti-plus me-1"></i>Add New Tenant
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
                <i class="ti ti-users me-2 text-primary"></i>Active & Trial Tenants
                <span class="badge bg-secondary ms-2">{{ $companies->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Company</th>
                            <th>Owner</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $company)
                            <tr>
                                <td class="ps-3">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($company->logo)
                                            <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}"
                                                class="rounded" style="width:32px;height:32px;object-fit:cover;">
                                        @else
                                            <div class="avatar-sm bg-primary-subtle rounded d-flex align-items-center justify-content-center"
                                                style="width:32px;height:32px;">
                                                <span class="text-primary fw-bold" style="font-size:13px;">
                                                    {{ strtoupper(substr($company->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $company->name }}</div>
                                            @if ($company->email)
                                                <small class="text-muted">{{ $company->email }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($company->owner)
                                        <div class="fw-semibold">{{ $company->owner->name }}</div>
                                        <small class="text-muted">{{ $company->owner->email }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($company->plan)
                                        <span class="badge bg-info-subtle text-info">{{ $company->plan->name }}</span>
                                    @else
                                        <span class="text-muted small">No Plan</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'active' => 'success',
                                            'trial' => 'warning',
                                            'suspended' => 'danger',
                                            'inactive' => 'secondary',
                                        ];
                                        $color = $statusColors[$company->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}-subtle text-{{ $color }}">
                                        {{ ucfirst($company->status) }}
                                    </span>
                                    @if ($company->status === 'trial' && $company->trial_ends_at)
                                        <br><small class="text-muted">Ends:
                                            {{ $company->trial_ends_at->format('d M Y') }}</small>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $company->created_at->format('d M Y') }}</small>
                                </td>
                                <td class="text-end pe-3">
                                    {{-- Impersonate Button --}}
                                    <form method="POST" action="{{ route('superadmin.companies.impersonate', $company) }}"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-soft-primary me-1"
                                            title="Login as this tenant"
                                            onclick="return confirm('Impersonate {{ addslashes($company->name) }}?')">
                                            <i class="ti ti-login"></i> Login As
                                        </button>
                                    </form>
                                    {{-- View Details --}}
                                    <a href="{{ route('superadmin.companies.show', $company) }}"
                                        class="btn btn-sm btn-soft-info me-1" title="View Details">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    {{-- Edit --}}
                                    <a href="{{ route('superadmin.companies.edit', $company) }}"
                                        class="btn btn-sm btn-soft-warning" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="ti ti-building-off d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                                    <h5>No Active Tenants</h5>
                                    <p class="small mb-3">No active or trial companies found.</p>
                                    <a href="{{ route('superadmin.companies.create') }}" class="btn btn-primary btn-sm">
                                        <i class="ti ti-plus me-1"></i>Create First Tenant
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($companies->hasPages())
                <div class="px-3 py-2 border-top">
                    {{ $companies->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection
