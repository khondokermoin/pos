@extends('layouts.admin_master')
@section('title', 'Cash Book — Accounts')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-book me-2 text-primary"></i>Cash Accounts</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Cash Book — Accounts</li>
                    </ol>
                </nav>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show"><i
                    class="ti ti-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row g-3">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Add New Account</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('company.cashbook.accounts.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Account Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="e.g., Main Cash" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Account Type <span
                                        class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">— Select Type —</option>
                                    <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="bank" {{ old('type') == 'bank' ? 'selected' : '' }}>Bank</option>
                                    <option value="mobile_banking" {{ old('type') == 'mobile_banking' ? 'selected' : '' }}>Mobile
                                        Banking</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Opening Balance</label>
                                <input type="number" name="balance" class="form-control" value="{{ old('balance', 0) }}"
                                    min="0" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes...">{{ old('notes') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="ti ti-plus me-1"></i> Add
                                Account</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All Accounts</h5>
                    </div>
                    <div class="card-body">
                        @if ($accounts->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="ti ti-book d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                                <h5 class="fw-semibold">No Accounts Yet</h5>
                                <p class="small">Add your first cash or bank account using the form on the left.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Balance</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($accounts as $account)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><strong>{{ $account->name }}</strong></td>
                                                <td><span
                                                        class="badge bg-info-lt text-info">{{ ucfirst(str_replace('_', ' ', $account->type)) }}</span>
                                                </td>
                                                <td>{{ number_format($account->balance, 2) }}</td>
                                                <td>{{ $account->notes ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
