{{-- resources/views/super-admin/plans/show.blade.php --}}
@extends('layouts.admin_master')

@section('content')
<div class="container-fluid " style="margin-top: 5px;">
    <div class="row" style="padding: 5px 0px">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">Plan Details: {{ $plan->name }}</h4>
                <div>
                    <a href="{{ route('superadmin.plans.edit', $plan->id) }}" class="btn btn-warning text-white me-2">
                        <i class="ti ti-edit"></i> Edit Plan
                    </a>
                    <a href="{{ route('superadmin.plans.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Plan Info Card --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="mb-0">${{ number_format($plan->price, 2) }}</h3>
                    <p class="text-muted">{{ ucfirst($plan->billing_cycle ?? 'monthly') }}</p>
                    <hr>
                    <div class="row text-start">
                        <div class="col-6"><strong>Trial:</strong> {{ $plan->trial_days }} Days</div>
                        <div class="col-6"><strong>Status:</strong> 
                            <span class="badge bg-{{ $plan->status == 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($plan->status) }}
                            </span>
                        </div>
                        <div class="col-6 mt-2"><strong>Users:</strong> {{ $plan->user_limit }}</div>
                        <div class="col-6 mt-2"><strong>Branches:</strong> {{ $plan->branch_limit }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Features & Subscribers Card --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Features Included</h5>
                    <ul class="list-group list-group-flush mb-4">
                        @forelse($plan->features as $feature)
                            <li class="list-group-item"><i class="ti ti-check text-success me-2"></i> {{ $feature }}</li>
                        @empty
                            <li class="list-group-item text-muted">No specific features listed.</li>
                        @endforelse
                    </ul>

                    <h5 class="card-title mt-4">Active Subscribers</h5>
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="ti ti-building-store me-2 fs-4"></i>
                        <div>
                            <strong>{{ $plan->companies->count() }}</strong> Companies are currently subscribed to this plan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection