@extends('layouts.admin_master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">Subscription Details</h4>
                <a href="{{ route('superadmin.subscriptions.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Company Information</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th width="40%">Company Name</th><td>{{ $subscription->company->name ?? 'N/A' }}</td></tr>
                        <tr><th>Email</th><td>{{ $subscription->company->email ?? 'N/A' }}</td></tr>
                        <tr><th>Subdomain</th><td>{{ $subscription->company->subdomain ?? 'N/A' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Plan Information</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th width="40%">Plan Name</th><td><strong>{{ $subscription->plan->name ?? 'N/A' }}</strong></td></tr>
                        <tr><th>Price</th><td>${{ number_format($subscription->plan->price ?? 0, 2) }}/{{ ucfirst($subscription->billing_cycle ?? 'monthly') }}</td></tr>
                        <tr><th>User Limit</th><td>{{ $subscription->plan->user_limit ?? 'N/A' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header"><h5 class="mb-0">Subscription Timeline</h5></div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3 border-end">
                    <small class="text-muted d-block">Started At</small>
                    <strong>{{ $subscription->started_at?->format('d M Y') ?? '-' }}</strong>
                </div>
                <div class="col-md-3 border-end">
                    <small class="text-muted d-block">Trial Ends</small>
                    <strong>{{ $subscription->trial_ends_at?->format('d M Y') ?? 'No Trial' }}</strong>
                </div>
                <div class="col-md-3 border-end">
                    <small class="text-muted d-block">Ends At</small>
                    <strong>{{ $subscription->ends_at?->format('d M Y') ?? 'Lifetime' }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-{{ $subscription->statusBadge() }} fs-6">{{ ucfirst($subscription->status) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection