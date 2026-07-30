@extends('layouts.super-admin')
@section('title', 'Support Tickets')
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-lifebuoy me-2 text-primary"></i>Support Tickets</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Support Tickets</li>
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

    {{-- Stats --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center py-3">
                    <h3 class="text-warning mb-0">{{ $stats['open'] }}</h3>
                    <small class="text-muted">Open</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center py-3">
                    <h3 class="text-info mb-0">{{ $stats['in_progress'] }}</h3>
                    <small class="text-muted">In Progress</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center py-3">
                    <h3 class="text-success mb-0">{{ $stats['resolved'] }}</h3>
                    <small class="text-muted">Resolved</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-secondary">
                <div class="card-body text-center py-3">
                    <h3 class="text-secondary mb-0">{{ $stats['closed'] }}</h3>
                    <small class="text-muted">Closed</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search subject / company..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress
                        </option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="priority" class="form-select form-select-sm">
                        <option value="">All Priority</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="ti ti-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('superadmin.support-tickets.index') }}"
                        class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="ti ti-x"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Subject</th>
                            <th>Company</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td class="ps-3">{{ $ticket->id }}</td>
                                <td>
                                    <a href="{{ route('superadmin.support-tickets.show', $ticket->id) }}"
                                        class="fw-semibold text-dark">
                                        {{ Str::limit($ticket->subject, 50) }}
                                    </a>
                                    @if ($ticket->admin_reply)
                                        <span class="badge bg-success-subtle text-success ms-1">Replied</span>
                                    @endif
                                </td>
                                <td>{{ optional($ticket->company)->name ?? '—' }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $ticket->priority_color }}-subtle text-{{ $ticket->priority_color }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $ticket->status_color }}-subtle text-{{ $ticket->status_color }}">
                                        {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>
                                <td><small>{{ $ticket->created_at->format('d M Y') }}</small></td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('superadmin.support-tickets.show', $ticket->id) }}"
                                        class="btn btn-sm btn-soft-primary me-1">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <form action="{{ route('superadmin.support-tickets.destroy', $ticket->id) }}"
                                        method="POST" class="d-inline" onsubmit="return confirm('Delete this ticket?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-soft-danger">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="ti ti-lifebuoy d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                                    <h5>No Support Tickets</h5>
                                    <p class="small">No tickets match your criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($tickets->hasPages())
                <div class="px-3 py-2 border-top">
                    {{ $tickets->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
