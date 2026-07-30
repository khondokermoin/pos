@extends('layouts.admin_master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">Active Subscriptions</h4>
                <a href="{{ route('superadmin.subscriptions.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Add Subscription
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white"><div class="card-body d-flex justify-content-between">
                <div><h6 class="text-white-50 mb-1">Total</h6><h2 class="mb-0 mt-2">{{ $stats['total'] }}</h2></div>
                <div class="fs-1 opacity-50"><i class="ti ti-file-invoice"></i></div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white"><div class="card-body d-flex justify-content-between">
                <div><h6 class="text-white-50 mb-1">Active</h6><h2 class="mb-0 mt-2">{{ $stats['active'] }}</h2></div>
                <div class="fs-1 opacity-50"><i class="ti ti-circle-check"></i></div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white"><div class="card-body d-flex justify-content-between">
                <div><h6 class="text-white-50 mb-1">On Trial</h6><h2 class="mb-0 mt-2">{{ $stats['trial'] }}</h2></div>
                <div class="fs-1 opacity-50"><i class="ti ti-hourglass-high"></i></div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white"><div class="card-body d-flex justify-content-between">
                <div><h6 class="text-white-50 mb-1">Expired</h6><h2 class="mb-0 mt-2">{{ $stats['expired'] }}</h2></div>
                <div class="fs-1 opacity-50"><i class="ti ti-alert-triangle"></i></div>
            </div></div>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('superadmin.subscriptions.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search Company</label>
                    <input type="text" name="search" class="form-control" placeholder="Company name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="trial" {{ request('status') == 'trial' ? 'selected' : '' }}>Trial</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter me-1"></i> Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('superadmin.subscriptions.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- Main Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th><th>Company</th><th>Plan</th><th>Status</th><th>End Date</th><th>Days Left</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $sub)
                        <tr>
                            <td>{{ $sub->id }}</td>
                            <td><strong>{{ $sub->company->name ?? 'N/A' }}</strong><br><small class="text-muted">{{ $sub->company->email ?? '' }}</small></td>
                            <td><span class="fw-bold">{{ $sub->plan->name ?? 'N/A' }}</span><br><small class="text-muted">${{ number_format($sub->plan->price ?? 0, 2) }}/{{ ucfirst($sub->billing_cycle ?? 'monthly') }}</small></td>
                            <td><span class="badge bg-{{ $sub->statusBadge() }}">{{ ucfirst($sub->status) }}</span></td>
                            <td>
                                @if($sub->ends_at)
                                    {{ $sub->ends_at->format('d M Y') }}
                                    @if($sub->isExpired()) <br><small class="text-danger">Expired</small> @endif
                                @else <span class="text-muted">∞</span> @endif
                            </td>
                            <td>
                                @if($sub->ends_at && !$sub->isExpired())
                                    <span class="badge bg-{{ $sub->daysRemaining() < 7 ? 'warning' : 'info' }}">{{ $sub->daysRemaining() }} days</span>
                                @else <span class="text-muted">-</span> @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('superadmin.subscriptions.show', $sub->id) }}" class="btn btn-sm btn-info text-white" title="View"><i class="ti ti-eye"></i></a>
                                    
                                    @if(in_array($sub->status, ['active', 'trial']))
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#extendModal" 
                                            data-action="{{ route('superadmin.subscriptions.extend', $sub->id) }}"
                                            data-company="{{ $sub->company->name ?? 'N/A' }}"
                                            data-date="{{ $sub->ends_at?->format('d M Y') ?? 'N/A' }}" title="Extend">
                                        <i class="ti ti-calendar-plus"></i>
                                    </button>
                                    @endif

                                    @if($sub->status === 'active')
                                    <form action="{{ route('superadmin.subscriptions.suspend', $sub->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Suspend this subscription?');">
                                        @csrf <button type="submit" class="btn btn-sm btn-warning text-white" title="Suspend"><i class="ti ti-pause-circle"></i></button>
                                    </form>
                                    @endif

                                    @if(in_array($sub->status, ['suspended', 'cancelled']))
                                    <form action="{{ route('superadmin.subscriptions.reactivate', $sub->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reactivate?');">
                                        @csrf <button type="submit" class="btn btn-sm btn-primary" title="Reactivate"><i class="ti ti-player-play"></i></button>
                                    </form>
                                    @endif

                                    @if(in_array($sub->status, ['active', 'trial', 'suspended']))
                                    <form action="{{ route('superadmin.subscriptions.cancel', $sub->id) }}" method="POST" class="d-inline" onsubmit="return confirm('CANCEL this subscription?');">
                                        @csrf <button type="submit" class="btn btn-sm btn-danger" title="Cancel"><i class="ti ti-x"></i></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No subscriptions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">Showing {{ $subscriptions->firstItem() ?? 0 }} to {{ $subscriptions->lastItem() ?? 0 }} of {{ $subscriptions->total() }}</div>
                {{ $subscriptions->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Include the single reusable modal --}}
@include('super-admin.subscriptions.partials.action_modal')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const extendModal = document.getElementById('extendModal');
    if (extendModal) {
        extendModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const form = document.getElementById('extendForm');
            // Update form action and dynamic text
            form.action = button.getAttribute('data-action');
            document.getElementById('extendCompanyName').textContent = button.getAttribute('data-company');
            document.getElementById('extendCurrentDate').textContent = button.getAttribute('data-date');
        });
    }
});
</script>
@endpush
@endsection