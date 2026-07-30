@extends('layouts.admin_master')

@section('content')
    <div class="container-fluid">

        <div class="page-title-head d-flex align-items-center gap-2 mb-3">
            <div class="flex-grow-1">
                <h4 class="fs-17 mb-0">Tenant Usage Report</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0 fs-13">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tenant Usage</li>
                </ol>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="mb-3">
            <a href="{{ route('superadmin.reports.index', ['type' => 'revenue']) }}"
                class="btn btn-outline-secondary btn-sm me-1">
                <i class="ti ti-chart-bar me-1"></i> Revenue Report
            </a>
            <a href="{{ route('superadmin.reports.index', ['type' => 'tenant-usage']) }}" class="btn btn-primary btn-sm">
                <i class="ti ti-chart-pie me-1"></i> Tenant Usage
            </a>
        </div>

        {{-- Summary Cards --}}
        <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 mb-3">
            <div class="col">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="text-white-50">Total Tenants</h6>
                        <h2 class="mb-0">{{ $companies->total() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6 class="text-white-50">Total Products</h6>
                        <h2 class="mb-0">{{ $productCounts->sum() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6 class="text-white-50">Total Sales</h6>
                        <h2 class="mb-0">{{ $saleCounts->sum() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6 class="text-white-50">Total Branches</h6>
                        <h2 class="mb-0">{{ $companies->sum('branches_count') }}</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tenant Table --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Per-Tenant Usage Breakdown</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Company</th>
                                <th>Plan</th>
                                <th>Status</th>
                                <th class="text-center">Users</th>
                                <th class="text-center">Branches</th>
                                <th class="text-center">Products</th>
                                <th class="text-center">Sales</th>
                                <th>Joined</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $i => $company)
                                <tr>
                                    <td class="text-muted fs-12">{{ $companies->firstItem() + $i }}</td>
                                    <td>
                                        <strong>{{ $company->name }}</strong>
                                        <div class="text-muted fs-12">{{ $company->email }}</div>
                                    </td>
                                    <td>{{ $company->plan->name ?? '—' }}</td>
                                    <td>
                                        @if ($company->status === 'active')
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        @elseif($company->status === 'trial')
                                            <span class="badge bg-warning-subtle text-warning">Trial</span>
                                        @elseif($company->status === 'suspended')
                                            <span class="badge bg-danger-subtle text-danger">Suspended</span>
                                        @else
                                            <span
                                                class="badge bg-secondary-subtle text-secondary">{{ ucfirst($company->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $company->users_count }}</td>
                                    <td class="text-center">{{ $company->branches_count }}</td>
                                    <td class="text-center">{{ $productCounts[$company->id] ?? 0 }}</td>
                                    <td class="text-center">{{ $saleCounts[$company->id] ?? 0 }}</td>
                                    <td class="text-muted fs-12">{{ $company->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('superadmin.companies.show', $company->id) }}"
                                            class="btn btn-sm btn-outline-info">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">No companies found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $companies->withQueryString()->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection
