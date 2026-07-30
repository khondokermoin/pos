@extends('layouts.super-admin')

@section('title', 'System Information')

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">System Information</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">System Info</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            <!-- PHP & Server Info -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-server me-2 text-primary"></i> Server & PHP
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold text-muted" style="width:45%">PHP Version</td>
                                    <td>
                                        <span
                                            class="badge bg-primary-subtle text-primary fs-13">{{ $info['php_version'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Laravel Version</td>
                                    <td>
                                        <span
                                            class="badge bg-danger-subtle text-danger fs-13">{{ $info['laravel_version'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Server Software</td>
                                    <td><code>{{ $info['server_software'] }}</code></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Memory Limit</td>
                                    <td><span class="badge bg-info-subtle text-info">{{ $info['memory_limit'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Max Upload Size</td>
                                    <td><span class="badge bg-info-subtle text-info">{{ $info['max_upload_size'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Max POST Size</td>
                                    <td><span class="badge bg-info-subtle text-info">{{ $info['max_post_size'] }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Database & App Info -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-database me-2 text-success"></i> Database & Application
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold text-muted" style="width:45%">Database Driver</td>
                                    <td>
                                        <span
                                            class="badge bg-success-subtle text-success fs-13">{{ strtoupper($info['database_driver']) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Database Name</td>
                                    <td><code>{{ $info['database_name'] }}</code></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">App Environment</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $info['app_env'] === 'production' ? 'success' : 'warning' }}-subtle text-{{ $info['app_env'] === 'production' ? 'success' : 'warning' }}">
                                            {{ ucfirst($info['app_env']) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Debug Mode</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $info['app_debug'] === 'Enabled' ? 'danger' : 'success' }}-subtle text-{{ $info['app_debug'] === 'Enabled' ? 'danger' : 'success' }}">
                                            {{ $info['app_debug'] }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Timezone</td>
                                    <td><code>{{ $info['timezone'] }}</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Storage Info -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-device-floppy me-2 text-warning"></i> Storage
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="p-3 bg-primary-subtle rounded">
                                    <h5 class="text-primary mb-1">{{ $info['storage_used'] }}</h5>
                                    <small class="text-muted">Used</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-success-subtle rounded">
                                    <h5 class="text-success mb-1">{{ $info['storage_free'] }}</h5>
                                    <small class="text-muted">Free</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-secondary-subtle rounded">
                                    <h5 class="text-secondary mb-1">{{ $info['storage_total'] }}</h5>
                                    <small class="text-muted">Total</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-bolt me-2 text-danger"></i> Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2">
                            <form method="POST" action="{{ route('superadmin.system.cache-clear') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning w-100"
                                    onclick="return confirm('Clear all caches?')">
                                    <i class="ti ti-refresh me-2"></i> Clear All Caches
                                </button>
                            </form>
                            <a href="{{ route('superadmin.system.backup') }}" class="btn btn-outline-success w-100">
                                <i class="ti ti-database-export me-2"></i> Go to Database Backup
                            </a>
                            <a href="{{ route('superadmin.system.logs') }}" class="btn btn-outline-info w-100">
                                <i class="ti ti-list me-2"></i> View Activity Logs
                            </a>
                            <a href="{{ route('superadmin.system.update') }}" class="btn btn-outline-primary w-100">
                                <i class="ti ti-refresh-alert me-2"></i> Update Application
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PHP Extensions -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-puzzle me-2 text-info"></i> Loaded PHP Extensions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-1">
                            @foreach (explode(', ', $info['php_extensions']) as $ext)
                                <span class="badge bg-light text-dark border">{{ trim($ext) }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
