@extends('layouts.admin_master')
@section('title', 'Cash Book — Transfers')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title mb-0"><i class="ti ti-arrows-exchange me-2 text-primary"></i>Cash Transfers</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Cash Book — Transfers</li>
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
                        <h5 class="mb-0">New Transfer</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('company.cashbook.transfers.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">From Account <span
                                        class="text-danger">*</span></label>
                                <select name="from_account_id" class="form-select" required>
                                    <option value="">— Select Account —</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">To Account <span class="text-danger">*</span></label>
                                <select name="to_account_id" class="form-select" required>
                                    <option value="">— Select Account —</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                                <input type="number" name="amount" class="form-control" min="0.01" step="0.01"
                                    value="{{ old('amount') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Transfer Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="transfer_date" class="form-control"
                                    value="{{ old('transfer_date', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="ti ti-arrows-exchange me-1"></i>
                                Transfer</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Transfer History</h5>
                    </div>
                    <div class="card-body">
                        @if ($transfers->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="ti ti-arrows-exchange d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                                <h5 class="fw-semibold">No Transfers Yet</h5>
                                <p class="small">Use the form to move money between accounts.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transfers as $transfer)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $transfer->fromAccount->name ?? '—' }}</td>
                                                <td>{{ $transfer->toAccount->name ?? '—' }}</td>
                                                <td>{{ number_format($transfer->amount, 2) }}</td>
                                                <td>{{ $transfer->transfer_date ?? '—' }}</td>
                                                <td>{{ $transfer->notes ?? '—' }}</td>
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
