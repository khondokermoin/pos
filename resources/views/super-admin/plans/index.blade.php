@extends('layouts.admin_master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">Pricing Plans</h4>
                <a href="{{ route('superadmin.plans.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Add New Plan
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Plan Name</th>
                                    <th>Price</th>
                                    <th>Trial</th>
                                    <th>Limits (User/Branch)</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $plan)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $plan->name }}</span>
                                        <br><small class="text-muted">{{ ucfirst($plan->billing_cycle ?? 'monthly') }}</small>
                                    </td>
                                    <td>${{ number_format($plan->price, 2) }}</td>
                                    <td>{{ $plan->trial_days }} Days</td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info">{{ $plan->user_limit }} Users</span>
                                        <span class="badge bg-warning-subtle text-warning">{{ $plan->branch_limit }} Branches</span>
                                    </td>
                                    <td>
                                        @if($plan->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('superadmin.plans.show', $plan->id) }}" class="btn btn-sm btn-info text-white" title="View"><i class="ti ti-eye"></i></a>
                                        <a href="{{ route('superadmin.plans.edit', $plan->id) }}" class="btn btn-sm btn-warning text-white" title="Edit"><i class="ti ti-edit"></i></a>
                                        <form action="{{ route('superadmin.plans.destroy', $plan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="ti ti-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No plans found. Please create one.</td>
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