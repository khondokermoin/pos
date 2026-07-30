@extends('layouts.admin_master')
@section('title', 'Update Application')
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-refresh-alert me-2 text-primary"></i>Update Application</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Update Application</li>
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
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-info-circle me-2"></i>Current Version</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-sm bg-primary-subtle rounded me-3 d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;">
                            <i class="ti ti-package fs-4 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Cloud POS Inventory</h5>
                            <span class="badge bg-primary">v{{ $currentVersion }}</span>
                        </div>
                    </div>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">PHP Version</td>
                            <td>{{ phpversion() }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Laravel Version</td>
                            <td>{{ app()->version() }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Environment</td>
                            <td>{{ app()->environment() }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-warning">
                <div class="card-header bg-warning-subtle">
                    <h5 class="mb-0 text-warning"><i class="ti ti-alert-triangle me-2"></i>Run Update</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong> This will run database migrations and clear all caches.
                        Make sure you have a database backup before proceeding.
                    </div>
                    <p class="text-muted small mb-3">The following commands will be executed:</p>
                    <ul class="small text-muted mb-3">
                        <li><code>php artisan migrate --force</code></li>
                        <li><code>php artisan cache:clear</code></li>
                        <li><code>php artisan config:clear</code></li>
                        <li><code>php artisan view:clear</code></li>
                        <li><code>php artisan route:clear</code></li>
                    </ul>
                    <form method="POST" action="{{ route('superadmin.system.update.run') }}"
                        onsubmit="return confirm('Are you sure you want to run the update? This will run migrations and clear all caches.')">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="ti ti-refresh-alert me-2"></i>Run Update Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
