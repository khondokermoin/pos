@extends('layouts.super-admin')

@section('title', 'Database Backup')

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Database Backup</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Database Backup</li>
                        </ol>
                    </div>
                </div>
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
            <!-- Generate Backup Card -->
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-body text-center py-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title rounded-circle bg-primary-subtle text-primary fs-36">
                                <i class="ti ti-database-export"></i>
                            </span>
                        </div>
                        <h5 class="card-title">Generate New Backup</h5>
                        <p class="text-muted mb-4">Create a full SQL dump of the database. The backup will be saved to
                            <code>storage/app/backups/</code>
                        </p>
                        <form method="POST" action="{{ route('superadmin.system.backup.generate') }}"
                            onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Generating...'">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="ti ti-database-export me-2"></i> Generate Backup Now
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card bg-info-subtle border-0">
                    <div class="card-body">
                        <h6 class="text-info mb-2"><i class="ti ti-info-circle me-1"></i> Backup Info</h6>
                        <ul class="list-unstyled mb-0 small text-muted">
                            <li><i class="ti ti-check text-success me-1"></i> Full database dump (all tables)</li>
                            <li><i class="ti ti-check text-success me-1"></i> Includes structure + data</li>
                            <li><i class="ti ti-check text-success me-1"></i> Uses mysqldump or PHP fallback</li>
                            <li><i class="ti ti-check text-success me-1"></i> Downloadable .sql file</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Backup Files List -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-files me-2 text-primary"></i>
                            Backup Files
                            <span class="badge bg-secondary ms-2">{{ count($backups) }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Filename</th>
                                        <th>Size</th>
                                        <th>Created At</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($backups as $backup)
                                        <tr>
                                            <td>
                                                <i class="ti ti-file-database text-primary me-2"></i>
                                                <code>{{ $backup['name'] }}</code>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $backup['size'] }}</span>
                                            </td>
                                            <td>{{ $backup['date'] }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('superadmin.system.backup.download', $backup['name']) }}"
                                                    class="btn btn-sm btn-success me-1">
                                                    <i class="ti ti-download me-1"></i> Download
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('superadmin.system.backup.delete', $backup['name']) }}"
                                                    class="d-inline" onsubmit="return confirm('Delete this backup file?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <i class="ti ti-database-off fs-24 d-block mb-2"></i>
                                                No backup files found. Generate your first backup.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
