@extends('layouts.super-admin')
@section('title', 'Ticket #' . $supportTicket->id)
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-lifebuoy me-2 text-primary"></i>Ticket #{{ $supportTicket->id }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.support-tickets.index') }}">Support Tickets</a>
                    </li>
                    <li class="breadcrumb-item active">Ticket #{{ $supportTicket->id }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('superadmin.support-tickets.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            {{-- Ticket Details --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $supportTicket->subject }}</h5>
                    <div>
                        <span
                            class="badge bg-{{ $supportTicket->priority_color }}-subtle text-{{ $supportTicket->priority_color }} me-1">
                            {{ ucfirst($supportTicket->priority) }}
                        </span>
                        <span
                            class="badge bg-{{ $supportTicket->status_color }}-subtle text-{{ $supportTicket->status_color }}">
                            {{ ucwords(str_replace('_', ' ', $supportTicket->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="bg-light rounded p-3 mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="ti ti-user-circle me-2 text-primary"></i>
                            <strong>{{ optional($supportTicket->user)->name ?? 'Unknown User' }}</strong>
                            <span class="text-muted small ms-2">—
                                {{ optional($supportTicket->company)->name ?? 'N/A' }}</span>
                            <span
                                class="text-muted small ms-auto">{{ $supportTicket->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                        <p class="mb-0" style="white-space:pre-wrap;">{{ $supportTicket->message }}</p>
                    </div>

                    @if ($supportTicket->admin_reply)
                        <div class="bg-primary-subtle rounded p-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ti ti-shield-check me-2 text-primary"></i>
                                <strong class="text-primary">Admin Reply</strong>
                                <span
                                    class="text-muted small ms-auto">{{ optional($supportTicket->replied_at)->format('d M Y, h:i A') }}</span>
                            </div>
                            <p class="mb-0" style="white-space:pre-wrap;">{{ $supportTicket->admin_reply }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Reply Form --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-message-reply me-2"></i>Reply & Update Status</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.support-tickets.update', $supportTicket->id) }}">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="open" {{ $supportTicket->status == 'open' ? 'selected' : '' }}>Open
                                </option>
                                <option value="in_progress"
                                    {{ $supportTicket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $supportTicket->status == 'resolved' ? 'selected' : '' }}>
                                    Resolved</option>
                                <option value="closed" {{ $supportTicket->status == 'closed' ? 'selected' : '' }}>Closed
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Admin Reply</label>
                            <textarea name="admin_reply" class="form-control" rows="5" placeholder="Write your reply here...">{{ $supportTicket->admin_reply }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-send me-1"></i>Update Ticket
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Ticket Info</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Ticket ID</td>
                            <td><strong>#{{ $supportTicket->id }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Company</td>
                            <td>{{ optional($supportTicket->company)->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Submitted By</td>
                            <td>{{ optional($supportTicket->user)->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Priority</td>
                            <td><span
                                    class="badge bg-{{ $supportTicket->priority_color }}-subtle text-{{ $supportTicket->priority_color }}">{{ ucfirst($supportTicket->priority) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td><span
                                    class="badge bg-{{ $supportTicket->status_color }}-subtle text-{{ $supportTicket->status_color }}">{{ ucwords(str_replace('_', ' ', $supportTicket->status)) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Created</td>
                            <td>{{ $supportTicket->created_at->format('d M Y') }}</td>
                        </tr>
                        @if ($supportTicket->replied_at)
                            <tr>
                                <td class="text-muted">Replied</td>
                                <td>{{ $supportTicket->replied_at->format('d M Y') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('superadmin.support-tickets.destroy', $supportTicket->id) }}" method="POST"
                        onsubmit="return confirm('Delete this ticket permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="ti ti-trash me-1"></i>Delete Ticket
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
