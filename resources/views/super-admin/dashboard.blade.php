@extends('layouts.admin_master')

@section('content')
    <div class="page-container">

        <div class="page-title-head d-flex align-items-center gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-17 mb-0">Dashboard</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0 fs-13">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">{{ config('app.name') }}</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>

        {{-- ── Row 1: Core SaaS Billing Stats ─────────────────────────────── --}}
        <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 mt-3">

            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-muted fs-13 text-uppercase">Monthly Revenue</h5>
                        <div class="d-flex align-items-center gap-2 my-3">
                            <h3 class="mb-0">৳{{ number_format($billingStats['monthly_revenue'], 2) }}</h3>
                            <i
                                class="ti ti-currency-taka ms-auto display-1 position-absolute opacity-25 text-muted widget-icon"></i>
                        </div>
                        <p class="mb-0 text-muted">
                            <span class="text-success me-2"><i class="ti ti-caret-up-filled"></i> Active</span>
                            <span class="text-nowrap">This month</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-muted fs-13 text-uppercase">Total Revenue</h5>
                        <div class="d-flex align-items-center gap-2 my-3">
                            <h3 class="mb-0">৳{{ number_format($billingStats['total_revenue'], 2) }}</h3>
                            <i
                                class="ti ti-receipt ms-auto display-1 position-absolute opacity-25 text-muted widget-icon"></i>
                        </div>
                        <p class="mb-0 text-muted">
                            <span class="text-info me-2"><i class="ti ti-check"></i> Success</span>
                            <span class="text-nowrap">All time</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-muted fs-13 text-uppercase">Active Subscriptions</h5>
                        <div class="d-flex align-items-center gap-2 my-3">
                            <h3 class="mb-0">{{ $billingStats['active_subscriptions'] }}</h3>
                            <i
                                class="ti ti-circle-check ms-auto display-1 position-absolute opacity-25 text-muted widget-icon"></i>
                        </div>
                        <p class="mb-0 text-muted">
                            <span class="text-warning me-2"><i class="ti ti-clock"></i>
                                {{ $billingStats['expiring_soon'] }}</span>
                            <span class="text-nowrap">Expiring soon</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-muted fs-13 text-uppercase">Available Plans</h5>
                        <div class="d-flex align-items-center gap-2 my-3">
                            <h3 class="mb-0">{{ $billingStats['total_plans'] }}</h3>
                            <i
                                class="ti ti-package ms-auto display-1 position-absolute opacity-25 text-muted widget-icon"></i>
                        </div>
                        <p class="mb-0 text-muted">
                            <span class="text-success me-2"><i class="ti ti-crown"></i> Active</span>
                            <span class="text-nowrap">Plans</span>
                        </p>
                    </div>
                </div>
            </div>

        </div><!-- end row -->

        {{-- ── Row 2: Company Status Stats ─────────────────────────────────── --}}
        <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1">

            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-muted fs-13 text-uppercase">Total Companies</h5>
                        <div class="d-flex align-items-center gap-2 my-3">
                            <h3 class="mb-0">{{ $companyStats['total'] }}</h3>
                            <i
                                class="ti ti-building-store ms-auto display-1 position-absolute opacity-25 text-muted widget-icon"></i>
                        </div>
                        <p class="mb-0 text-muted">
                            <span class="text-primary me-2"><i class="ti ti-users"></i> Registered</span>
                            <span class="text-nowrap">Tenants</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-muted fs-13 text-uppercase">Active Companies</h5>
                        <div class="d-flex align-items-center gap-2 my-3">
                            <h3 class="mb-0">{{ $companyStats['active'] }}</h3>
                            <i
                                class="ti ti-circle-check ms-auto display-1 position-absolute opacity-25 text-muted widget-icon"></i>
                        </div>
                        <p class="mb-0 text-muted">
                            <span class="text-success me-2"><i class="ti ti-check"></i> Live</span>
                            <span class="text-nowrap">Subscribed</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-muted fs-13 text-uppercase">On Trial</h5>
                        <div class="d-flex align-items-center gap-2 my-3">
                            <h3 class="mb-0">{{ $companyStats['trial'] }}</h3>
                            <i
                                class="ti ti-clock ms-auto display-1 position-absolute opacity-25 text-muted widget-icon"></i>
                        </div>
                        <p class="mb-0 text-muted">
                            <span class="text-warning me-2"><i class="ti ti-hourglass"></i> Trial</span>
                            <span class="text-nowrap">Period</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-muted fs-13 text-uppercase">Suspended</h5>
                        <div class="d-flex align-items-center gap-2 my-3">
                            <h3 class="mb-0">{{ $companyStats['suspended'] }}</h3>
                            <i class="ti ti-ban ms-auto display-1 position-absolute opacity-25 text-muted widget-icon"></i>
                        </div>
                        <p class="mb-0 text-muted">
                            <span class="text-danger me-2"><i class="ti ti-alert-triangle"></i> Blocked</span>
                            <span class="text-nowrap">Accounts</span>
                        </p>
                    </div>
                </div>
            </div>

        </div><!-- end row -->

        {{-- ── Row 3: Charts ───────────────────────────────────────────────── --}}
        <div class="row mt-2">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Revenue Trend (Last 6 Months)</h5>
                        <a href="{{ route('superadmin.reports.index', ['type' => 'revenue']) }}"
                            class="btn btn-sm btn-outline-primary">Full Report</a>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Plan Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="planChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Row 4: Expiring Subscriptions ───────────────────────────────── --}}
        <div class="row mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="ti ti-clock me-2 text-warning"></i>Expiring in Next 7 Days</h5>
                        <a href="{{ route('superadmin.subscriptions.index', ['status' => 'active']) }}"
                            class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Company</th>
                                    <th>Plan</th>
                                    <th>Expires</th>
                                    <th>Days Left</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expiringSoon as $sub)
                                    <tr>
                                        <td><strong>{{ $sub->company->name ?? 'N/A' }}</strong></td>
                                        <td>{{ $sub->plan->name ?? 'N/A' }}</td>
                                        <td>{{ $sub->ends_at->format('d M Y') }}</td>
                                        <td><span
                                                class="badge bg-{{ $sub->daysRemaining() <= 3 ? 'danger' : 'warning' }}">{{ $sub->daysRemaining() }}d</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('superadmin.subscriptions.show', $sub->id) }}"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">No subscriptions expiring
                                            soon.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Row 5: Recent Companies + Recent Transactions ───────────────── --}}
        <div class="row mt-2">

            {{-- Recent Company Signups --}}
            <div class="col-xxl-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="ti ti-building-store me-2 text-primary"></i>Recent Company Signups
                        </h5>
                        <a href="{{ route('superadmin.companies.index') }}" class="btn btn-sm btn-outline-primary">View
                            All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Company</th>
                                        <th>Plan</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentCompanies as $company)
                                        <tr>
                                            <td>
                                                <strong>{{ $company->name }}</strong>
                                                <div class="text-muted fs-12">{{ $company->email }}</div>
                                            </td>
                                            <td>{{ $company->plan->name ?? '—' }}</td>
                                            <td>
                                                @if ($company->status === 'active')
                                                    <span class="badge bg-success-subtle text-success">Active</span>
                                                @elseif($company->status === 'trial')
                                                    <span class="badge bg-warning-subtle text-warning">Trial</span>
                                                @elseif($company->status === 'suspended')
                                                    <span class="badge bg-danger-subtle text-danger">Suspended</span>
                                                @else
                                                    <span
                                                        class="badge bg-secondary-subtle text-secondary">{{ ucfirst($company->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-muted fs-12">{{ $company->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('superadmin.companies.show', $company->id) }}"
                                                    class="btn btn-xs btn-outline-info btn-sm">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-3 text-muted">No companies yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Transactions --}}
            <div class="col-xxl-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="ti ti-receipt me-2 text-success"></i>Recent Transactions</h5>
                        <a href="{{ route('superadmin.transactions.index') }}"
                            class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Company</th>
                                        <th>Plan</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTransactions as $txn)
                                        <tr>
                                            <td><strong>{{ $txn->company->name ?? 'N/A' }}</strong></td>
                                            <td>{{ $txn->subscription->plan->name ?? '—' }}</td>
                                            <td><span
                                                    class="text-success fw-semibold">৳{{ number_format($txn->amount, 2) }}</span>
                                            </td>
                                            <td>{{ ucfirst($txn->payment_method ?? '—') }}</td>
                                            <td class="text-muted fs-12">{{ $txn->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-3 text-muted">No transactions yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- end row -->

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const revenueCtx = document.getElementById('revenueChart')?.getContext('2d');
        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: @json($revenueChart->pluck('label')),
                    datasets: [{
                        label: 'Revenue (৳)',
                        data: @json($revenueChart->pluck('total')),
                        borderColor: '#1a56db',
                        backgroundColor: 'rgba(26,86,219,0.1)',
                        tension: 0.4,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        const planCtx = document.getElementById('planChart')?.getContext('2d');
        if (planCtx) {
            new Chart(planCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($planDistribution->pluck('name')),
                    datasets: [{
                        data: @json($planDistribution->pluck('subscriptions_count')),
                        backgroundColor: ['#1a56db', '#16bdca', '#e74694', '#ff5a1f', '#0e9f6e'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    </script>
@endpush
