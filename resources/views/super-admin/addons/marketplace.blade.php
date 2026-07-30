@extends('layouts.super-admin')
@section('title', 'Addon Marketplace')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ti ti-world me-2 text-primary"></i>Addon Marketplace</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.addons.index') }}">Addons</a></li>
                        <li class="breadcrumb-item active">Marketplace</li>
                    </ol>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Header Banner -->
        <div class="card bg-primary text-white mb-4">
            <div class="card-body py-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="text-white mb-1"><i class="ti ti-shopping-bag me-2"></i>Addon Marketplace</h3>
                        <p class="mb-0 opacity-75">Discover and install powerful addons to extend your platform's
                            functionality.</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('superadmin.addons.index') }}" class="btn btn-light btn-sm">
                            <i class="ti ti-puzzle me-1"></i> View Installed Addons
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if ($addons->isEmpty())
            <!-- Empty State -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ti ti-world-off d-block mb-3 text-muted" style="font-size: 4rem; opacity: 0.4;"></i>
                    <h4 class="text-muted">No Addons Available</h4>
                    <p class="text-muted mb-4">
                        The marketplace is currently empty. All available addons have already been installed,
                        or no addons have been added to the system yet.
                    </p>
                    <a href="{{ route('superadmin.addons.index') }}" class="btn btn-primary">
                        <i class="ti ti-puzzle me-1"></i> View Installed Addons
                    </a>
                </div>
            </div>
        @else
            <!-- Addons Grid -->
            <div class="row">
                @foreach ($addons as $addon)
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center pt-4">
                                <div class="avatar-lg mx-auto mb-3">
                                    @if ($addon->icon)
                                        <img src="{{ $addon->icon }}" alt="{{ $addon->name }}"
                                            class="rounded-circle img-fluid"
                                            style="width:64px;height:64px;object-fit:cover;">
                                    @else
                                        <div class="bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                            style="width:64px;height:64px;">
                                            <i class="ti ti-puzzle fs-28 text-primary"></i>
                                        </div>
                                    @endif
                                </div>
                                <h5 class="card-title mb-1">{{ $addon->name }}</h5>
                                @if ($addon->version)
                                    <span class="badge bg-info-subtle text-info mb-2">v{{ $addon->version }}</span>
                                @endif
                                @if ($addon->description)
                                    <p class="text-muted small mb-3">{{ Str::limit($addon->description, 80) }}</p>
                                @endif
                                <div class="mb-3">
                                    @if ($addon->price > 0)
                                        <span
                                            class="fs-5 fw-bold text-success">${{ number_format($addon->price, 2) }}</span>
                                    @else
                                        <span class="badge bg-success fs-6 px-3 py-2">FREE</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0 pb-3 text-center">
                                <form method="POST" action="{{ route('superadmin.addons.store') }}">
                                    @csrf
                                    <input type="hidden" name="name" value="{{ $addon->name }}">
                                    <input type="hidden" name="description" value="{{ $addon->description }}">
                                    <input type="hidden" name="version" value="{{ $addon->version }}">
                                    <input type="hidden" name="price" value="{{ $addon->price }}">
                                    <input type="hidden" name="is_active" value="1">
                                    <button type="submit" class="btn btn-primary btn-sm w-100"
                                        onclick="return confirm('Install {{ addslashes($addon->name) }}?')">
                                        <i class="ti ti-download me-1"></i>
                                        {{ $addon->price > 0 ? 'Purchase & Install' : 'Install Free' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Coming Soon Section -->
        <div class="card border-dashed mt-2">
            <div class="card-body text-center py-4">
                <i class="ti ti-rocket fs-36 text-primary mb-2 d-block"></i>
                <h5>More Addons Coming Soon</h5>
                <p class="text-muted small mb-0">
                    We're constantly adding new addons. Check back regularly for new features and integrations.
                </p>
            </div>
        </div>
    </div>
@endsection
