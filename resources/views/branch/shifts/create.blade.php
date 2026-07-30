@extends('layouts.admin_master')
@section('title', 'Open New Shift')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">

                {{-- Page Header --}}
                <div class="text-center mb-4">
                    <div
                        style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#4f6ef7,#6c3de0);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <i class="ti ti-lock-open" style="font-size:28px;color:#fff;"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Open Cash Register</h4>
                    <p class="text-muted small">Start a new shift to begin selling on the POS terminal.</p>
                </div>

                {{-- Error Alert --}}
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Open Shift Form --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-clock me-2"></i>New Shift Details
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('branch.shifts.store') }}" method="POST">
                            @csrf

                            {{-- Hidden branch_id --}}
                            <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">

                            {{-- Branch Info (read-only display) --}}
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-semibold">Branch</label>
                                <div class="form-control bg-light text-muted">
                                    <i class="ti ti-building-store me-2"></i>
                                    {{ auth()->user()->branch->name ?? 'N/A' }}
                                </div>
                            </div>

                            {{-- Cashier Info (read-only display) --}}
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-semibold">Cashier</label>
                                <div class="form-control bg-light text-muted">
                                    <i class="ti ti-user me-2"></i>
                                    {{ auth()->user()->name }}
                                </div>
                            </div>

                            {{-- Opening Balance --}}
                            <div class="mb-4">
                                <label for="opening_balance" class="form-label fw-semibold">
                                    Opening Cash Balance <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text fw-bold">৳</span>
                                    <input type="number" id="opening_balance" name="opening_balance"
                                        class="form-control form-control-lg text-end fw-bold @error('opening_balance') is-invalid @enderror"
                                        value="{{ old('opening_balance', '0.00') }}" step="0.01" min="0"
                                        placeholder="0.00" required autofocus>
                                    @error('opening_balance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Enter the cash amount in the drawer at the start of this shift.</div>
                            </div>

                            {{-- Date/Time Display --}}
                            <div class="mb-4 p-3 rounded" style="background:#f8f9fa; border:1px dashed #dee2e6;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small"><i class="ti ti-calendar me-1"></i>Shift Start
                                        Time</span>
                                    <span class="fw-semibold" id="current-datetime"></span>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                    <i class="ti ti-lock-open me-2"></i>Open Shift & Go to POS
                                </button>
                                <a href="{{ route('branch.shifts.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Back to Shifts
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Info Card --}}
                <div class="card mt-3 border-0" style="background:#eff6ff;">
                    <div class="card-body py-3 px-4">
                        <h6 class="text-primary mb-2"><i class="ti ti-info-circle me-1"></i>What is a Shift?</h6>
                        <ul class="mb-0 small text-muted ps-3">
                            <li>A shift tracks all sales made during your working session.</li>
                            <li>Enter the opening cash balance in the drawer.</li>
                            <li>Close the shift at the end of your session to reconcile cash.</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function updateDateTime() {
                const now = new Date();
                document.getElementById('current-datetime').textContent =
                    now.toLocaleString('en-BD', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
            }
            updateDateTime();
            setInterval(updateDateTime, 1000);
        </script>
    @endpush
@endsection
