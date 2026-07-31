@extends('layouts.admin_master')

@section('content')
    <div class="container-fluid">

        {{-- ✅ Success alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="ti ti-circle-check fs-4 me-2 flex-shrink-0"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- ❌ Generic error alert --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="ti ti-alert-circle fs-4 me-2 flex-shrink-0"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- ⚠️ Warning alert --}}
        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="ti ti-alert-triangle fs-4 me-2 flex-shrink-0"></i>
                <div>{{ session('warning') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 🚫 DOWNGRADE EXPLOIT BLOCKED — Rich actionable error --}}
        @if (session('downgrade_error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-start">
                    <i class="ti ti-shield-x fs-3 me-3 flex-shrink-0 text-danger mt-1"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1 fw-bold">
                            <i class="ti ti-ban me-1"></i> Plan Switch Blocked — Usage Limit Exceeded
                        </h6>
                        <div class="mb-0">{!! session('downgrade_error') !!}</div>
                        <hr class="my-2">
                        <p class="mb-0 small text-muted">
                            <i class="ti ti-info-circle me-1"></i>
                            To switch to a lower-tier plan, please reduce your active resources first, then try again.
                            Need help? <a href="{{ route('company.subscription.index') }}" class="alert-link">View your
                                current plan</a>.
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">My Subscription</h4>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                @if ($subscription && $subscription->status !== 'cancelled')
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-{{ $subscription->statusBadge() }} fs-6 me-3">
                                            {{ ucfirst($subscription->status) }}
                                        </span>
                                        <h4 class="mb-0">{{ $subscription->plan->name ?? 'Unknown Plan' }}</h4>
                                    </div>
                                    <p class="text-muted mb-1">
                                        <i class="ti ti-calendar me-1"></i>
                                        Started: <strong>{{ $subscription->started_at?->format('d M Y') ?? 'N/A' }}</strong>
                                        &nbsp;|&nbsp;
                                        Expires:
                                        <strong>{{ $subscription->ends_at?->format('d M Y') ?? 'Lifetime' }}</strong>
                                    </p>
                                    @if ($subscription->ends_at && !$subscription->isExpired())
                                        <p class="text-muted mb-0">
                                            <i class="ti ti-clock me-1"></i>
                                            <strong
                                                class="text-{{ $subscription->daysRemaining() < 7 ? 'danger' : 'success' }}">
                                                {{ $subscription->daysRemaining() }} days remaining
                                            </strong>
                                        </p>
                                    @elseif($subscription->isExpired())
                                        <p class="text-danger mb-0"><i class="ti ti-alert-triangle me-1"></i> Your
                                            subscription has expired. Please renew.</p>
                                    @endif
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <h2 class="mb-0 text-primary">
                                        ৳{{ number_format($subscription->plan->price ?? 0, 2) }}
                                        <small
                                            class="fs-6 text-muted">/{{ ucfirst($subscription->billing_cycle ?? 'month') }}</small>
                                    </h2>
                                    <div class="mt-2">
                                        <span class="badge bg-info-subtle text-info me-1">
                                            <i class="ti ti-users me-1"></i>{{ $subscription->plan->user_limit ?? '—' }}
                                            Users
                                        </span>
                                        <span class="badge bg-warning-subtle text-warning">
                                            <i
                                                class="ti ti-building-store me-1"></i>{{ $subscription->plan->branch_limit ?? '—' }}
                                            Branches
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning d-flex align-items-center">
                        <i class="ti ti-alert-circle fs-3 me-3"></i>
                        <div>
                            <strong>No Active Subscription!</strong>
                            You don't have an active subscription. Please choose a plan below to get started.
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── Usage snapshot (shown only when a subscription is active) ── --}}
        @if ($subscription && $subscription->status !== 'cancelled')
            @php
                $currentBranchCount = $company->branches()->count();
                $currentUserCount = $company->users()->count();
            @endphp
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card border-0 bg-light shadow-sm">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <span class="fw-semibold text-muted small"><i class="ti ti-chart-bar me-1"></i>Your Current
                                    Usage:</span>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">
                                    <i class="ti ti-users me-1"></i>
                                    <strong>{{ $currentUserCount }}</strong> /
                                    {{ $subscription->plan->user_limit == -1 || $subscription->plan->user_limit === null ? '∞' : $subscription->plan->user_limit }}
                                    Users
                                </span>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">
                                    <i class="ti ti-building-store me-1"></i>
                                    <strong>{{ $currentBranchCount }}</strong> /
                                    {{ $subscription->plan->branch_limit == -1 || $subscription->plan->branch_limit === null ? '∞' : $subscription->plan->branch_limit }}
                                    Branches
                                </span>
                                <span class="text-muted small ms-auto">
                                    <i class="ti ti-info-circle me-1"></i>Plans that cannot accommodate your current usage
                                    will be marked below.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-12">
                <h5 class="mb-3"><i class="ti ti-package me-2"></i>Available Plans</h5>
            </div>
            @forelse($plans as $plan)
                @php
                    // ── Per-card downgrade eligibility check (UI-only, visual hint) ──
                    // This mirrors the server-side guard so users see BEFORE clicking.
                    $planBlocked = false;
                    $planBlockReasons = [];

                    if (isset($currentBranchCount)) {
                        $bl = $plan->branch_limit;
                        if ($bl !== null && $bl != -1 && $currentBranchCount > $bl) {
                            $excess = $currentBranchCount - $bl;
                            $planBlocked = true;
                            $planBlockReasons[] =
                                "Delete {$excess} branch" .
                                ($excess !== 1 ? 'es' : '') .
                                " (have {$currentBranchCount}, limit {$bl})";
                        }
                    }
                    if (isset($currentUserCount)) {
                        $ul = $plan->user_limit;
                        if ($ul !== null && $ul != -1 && $currentUserCount > $ul) {
                            $excess = $currentUserCount - $ul;
                            $planBlocked = true;
                            $planBlockReasons[] =
                                "Remove {$excess} user" .
                                ($excess !== 1 ? 's' : '') .
                                " (have {$currentUserCount}, limit {$ul})";
                        }
                    }

                    $isCurrentActivePlan =
                        $subscription && $subscription->plan_id == $plan->id && !$subscription->isExpired();
                @endphp
                <div class="col-md-4 mb-3">
                    <div
                        class="card h-100 border-{{ $isCurrentActivePlan ? 'primary border-2' : ($planBlocked ? 'danger border-1' : '0') }} shadow-sm {{ $planBlocked ? 'opacity-85' : '' }}">
                        @if ($isCurrentActivePlan)
                            <div class="card-header bg-primary text-white text-center py-1">
                                <small><i class="ti ti-check me-1"></i> Current Plan</small>
                            </div>
                        @elseif($planBlocked)
                            <div class="card-header bg-danger-subtle text-danger text-center py-1">
                                <small><i class="ti ti-ban me-1"></i> Cannot Downgrade — Usage Too High</small>
                            </div>
                        @endif
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $plan->name }}</h5>
                            <h2 class="text-primary my-3">
                                ৳{{ number_format($plan->price, 2) }}
                                <small class="fs-6 text-muted">/{{ ucfirst($plan->billing_cycle ?? 'month') }}</small>
                            </h2>
                            <div class="mb-3">
                                @php
                                    $uLimit = $plan->user_limit;
                                    $bLimit = $plan->branch_limit;
                                    $uOver =
                                        isset($currentUserCount) &&
                                        $uLimit !== null &&
                                        $uLimit != -1 &&
                                        $currentUserCount > $uLimit;
                                    $bOver =
                                        isset($currentBranchCount) &&
                                        $bLimit !== null &&
                                        $bLimit != -1 &&
                                        $currentBranchCount > $bLimit;
                                @endphp
                                <span
                                    class="badge {{ $uOver ? 'bg-danger text-white' : 'bg-info-subtle text-info' }} me-1">
                                    <i class="ti ti-users me-1"></i>
                                    {{ $uLimit == -1 || $uLimit === null ? 'Unlimited' : $uLimit }} Users
                                    @if ($uOver)
                                        <i class="ti ti-alert-triangle ms-1"></i>
                                    @endif
                                </span>
                                <span
                                    class="badge {{ $bOver ? 'bg-danger text-white' : 'bg-warning-subtle text-warning' }}">
                                    <i class="ti ti-building-store me-1"></i>
                                    {{ $bLimit == -1 || $bLimit === null ? 'Unlimited' : $bLimit }} Branches
                                    @if ($bOver)
                                        <i class="ti ti-alert-triangle ms-1"></i>
                                    @endif
                                </span>
                                @if ($plan->trial_days > 0)
                                    <span class="badge bg-success-subtle text-success mt-1">{{ $plan->trial_days }}-day
                                        Trial</span>
                                @endif
                            </div>
                            <ul class="list-unstyled text-start mb-3">
                                @if (is_array($plan->features))
                                    @foreach ($plan->features as $feature)
                                        <li class="mb-1"><i
                                                class="ti ti-check text-success me-2"></i>{{ $feature }}</li>
                                    @endforeach
                                @endif
                            </ul>

                            {{-- Downgrade block reasons list --}}
                            @if ($planBlocked)
                                <div class="alert alert-danger py-2 px-3 text-start mb-3 small">
                                    <strong><i class="ti ti-shield-x me-1"></i>To switch to this plan, you must:</strong>
                                    <ul class="mb-0 mt-1 ps-3">
                                        @foreach ($planBlockReasons as $reason)
                                            <li>{{ $reason }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if ($isCurrentActivePlan)
                                <button class="btn btn-outline-primary w-100" disabled>
                                    <i class="ti ti-check me-1"></i> Active Plan
                                </button>
                            @elseif($planBlocked)
                                <button class="btn btn-outline-danger w-100" disabled
                                    title="Reduce your usage to unlock this plan">
                                    <i class="ti ti-lock me-1"></i> Reduce Usage to Unlock
                                </button>
                            @else
                                <form action="{{ route('company.subscription.subscribe', $plan->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="billing_cycle"
                                        value="{{ $plan->billing_cycle ?? 'monthly' }}">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="ti ti-credit-card me-1"></i>
                                        Subscribe — ৳{{ number_format($plan->price, 2) }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">No plans are currently available. Please contact support.</div>
                </div>
            @endforelse
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ti ti-receipt me-2"></i>Payment History</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Invoice</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $txn)
                                        <tr>
                                            <td><code>{{ $txn->transaction_id }}</code></td>
                                            <td><strong>{{ $txn->currency }} {{ number_format($txn->amount, 2) }}</strong>
                                            </td>
                                            <td>{{ ucfirst($txn->payment_method ?? '—') }}</td>
                                            <td>
                                                @php
                                                    $badge = match ($txn->status) {
                                                        'success' => 'success',
                                                        'pending' => 'warning',
                                                        'failed' => 'danger',
                                                        'refunded' => 'info',
                                                        default => 'secondary',
                                                    };
                                                @endphp
                                                <span
                                                    class="badge bg-{{ $badge }}">{{ ucfirst($txn->status) }}</span>
                                            </td>
                                            <td>{{ $txn->created_at->format('d M Y, h:i A') }}</td>
                                            <td>
                                                @if ($txn->subscription && $txn->subscription->invoice_number)
                                                    <a href="{{ route('company.subscription.invoice.download', $txn->subscription->invoice_number) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Download Invoice">
                                                        <i class="ti ti-download me-1"></i> PDF
                                                    </a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="ti ti-receipt-off fs-3 d-block mb-2"></i>
                                                No payment history found.
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
