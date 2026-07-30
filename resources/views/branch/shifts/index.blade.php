@extends('layouts.admin_master')
@section('title', 'Shift Management')

@section('content')
    <div class="container-fluid">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-1 fw-bold">Shift Management</h4>
                <p class="text-muted mb-0 small">Manage your cash register sessions</p>
            </div>
            <a href="{{ route('branch.shifts.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Open New Shift
            </a>
        </div>

        {{-- Active Shift Alert --}}
        @php
            $openShift = $shifts->firstWhere('status', 'open');
        @endphp
        @if ($openShift)
            <div class="alert alert-success d-flex align-items-center gap-3 mb-4">
                <i class="ti ti-circle-check fs-3"></i>
                <div>
                    <strong>Shift is Open</strong> — Opened at {{ $openShift->created_at->format('h:i A, d M Y') }}
                    · Opening Balance: ৳{{ number_format($openShift->opening_balance, 2) }}
                </div>
                <a href="{{ route('branch.pos.index') }}" class="btn btn-success btn-sm ms-auto">
                    <i class="ti ti-device-desktop-analytics me-1"></i> Go to POS
                </a>
            </div>
        @else
            <div class="alert alert-warning d-flex align-items-center gap-3 mb-4">
                <i class="ti ti-alert-triangle fs-3"></i>
                <div>
                    <strong>No Open Shift</strong> — You must open a shift before using the POS terminal.
                </div>
                <a href="{{ route('branch.shifts.create') }}" class="btn btn-warning btn-sm ms-auto">
                    <i class="ti ti-lock-open me-1"></i> Open Shift Now
                </a>
            </div>
        @endif

        {{-- Shifts Table --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ti ti-clock me-2"></i>Shift History</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Opened At</th>
                                <th>Closed At</th>
                                <th>Opening Balance</th>
                                <th>Closing Balance</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shifts as $shift)
                                <tr>
                                    <td>{{ $shift->id }}</td>
                                    <td>{{ $shift->created_at->format('d M Y, h:i A') }}</td>
                                    <td>{{ $shift->updated_at && $shift->status === 'closed' ? $shift->updated_at->format('d M Y, h:i A') : '—' }}
                                    </td>
                                    <td>৳{{ number_format($shift->opening_balance, 2) }}</td>
                                    <td>{{ $shift->closing_balance ? '৳' . number_format($shift->closing_balance, 2) : '—' }}
                                    </td>
                                    <td>
                                        @if ($shift->status === 'open')
                                            <span class="badge bg-success-lt text-success">Open</span>
                                        @else
                                            <span class="badge bg-secondary-lt text-secondary">Closed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($shift->status === 'open')
                                            <form action="{{ route('branch.shifts.close', $shift) }}" method="POST"
                                                onsubmit="return confirm('Close this shift?')">
                                                @csrf
                                                <div class="input-group input-group-sm" style="max-width: 220px;">
                                                    <input type="number" name="closing_balance" class="form-control"
                                                        placeholder="Closing balance" step="0.01" min="0"
                                                        required>
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="ti ti-lock"></i> Close
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="ti ti-clock-off" style="font-size: 2rem; opacity: .3;"></i><br>
                                        No shifts found. Open your first shift to start selling.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($shifts->hasPages())
                <div class="card-footer">
                    {{ $shifts->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
