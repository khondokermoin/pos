@extends('layouts.admin_master')

@section('title', 'Announcements')

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-speakerphone me-2 text-primary"></i>Announcements</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Announcements</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2">
                <i class="ti ti-bell me-1"></i>{{ $announcements->total() }} Active
            </span>
        </div>
    </div>

    {{-- Announcements List --}}
    @forelse($announcements as $announcement)
        @php
            $typeConfig = match ($announcement->type ?? 'info') {
                'warning' => ['color' => 'warning', 'icon' => 'ti-alert-triangle', 'bg' => 'bg-warning-subtle'],
                'danger' => ['color' => 'danger', 'icon' => 'ti-alert-circle', 'bg' => 'bg-danger-subtle'],
                'success' => ['color' => 'success', 'icon' => 'ti-circle-check', 'bg' => 'bg-success-subtle'],
                'primary' => ['color' => 'primary', 'icon' => 'ti-star', 'bg' => 'bg-primary-subtle'],
                default => ['color' => 'info', 'icon' => 'ti-info-circle', 'bg' => 'bg-info-subtle'],
            };
        @endphp
        <div class="card mb-3 border-start border-{{ $typeConfig['color'] }} border-3">
            <div class="card-body">
                <div class="d-flex align-items-start gap-3">
                    {{-- Icon --}}
                    <div class="rounded-circle {{ $typeConfig['bg'] }} d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:48px;height:48px;">
                        <i class="ti {{ $typeConfig['icon'] }} fs-4 text-{{ $typeConfig['color'] }}"></i>
                    </div>

                    {{-- Content --}}
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h5 class="mb-0 fw-semibold">{{ $announcement->title }}</h5>
                            <div class="d-flex align-items-center gap-2 ms-3">
                                <span
                                    class="badge bg-{{ $typeConfig['color'] }}-subtle text-{{ $typeConfig['color'] }} border border-{{ $typeConfig['color'] }}-subtle">
                                    {{ ucfirst($announcement->type ?? 'info') }}
                                </span>
                                <small class="text-muted text-nowrap">
                                    <i class="ti ti-clock me-1"></i>
                                    {{ $announcement->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>

                        <p class="text-muted mb-0">{{ $announcement->content ?? ($announcement->message ?? '') }}</p>

                        @if ($announcement->created_at)
                            <small class="text-muted mt-1 d-block">
                                <i class="ti ti-calendar me-1"></i>
                                Posted on {{ $announcement->created_at->format('d M Y, h:i A') }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="ti ti-speakerphone d-block mb-3" style="font-size:4rem;opacity:0.3;"></i>
                <h4 class="fw-semibold">No Announcements</h4>
                <p class="mb-0">There are no active announcements at this time.</p>
                <p class="small text-muted mt-1">Check back later for updates from the platform team.</p>
            </div>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if ($announcements->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $announcements->links() }}
        </div>
    @endif
@endsection
