@extends('layouts.admin_master')

@section('title', 'Expenses')

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-receipt-2 me-2 text-primary"></i>Expenses</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Expenses</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#expenseModal"
                onclick="openAddModal()">
                <i class="ti ti-plus me-1"></i> Add Expense
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats Row --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Expenses (All Time)</h6>
                        <h3 class="mb-0">৳{{ number_format($totalAllTime, 2) }}</h3>
                    </div>
                    <i class="ti ti-currency-taka fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">This Month</h6>
                        <h3 class="mb-0">৳{{ number_format($totalThisMonth, 2) }}</h3>
                    </div>
                    <i class="ti ti-calendar-stats fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-secondary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Entries</h6>
                        <h2 class="mb-0">{{ $totalEntries }}</h2>
                    </div>
                    <i class="ti ti-list fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('company.expenses.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                        value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                        value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Branch</label>
                    <select name="branch_id" class="form-select form-select-sm">
                        <option value="">All Branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="ti ti-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('company.expenses.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="ti ti-x me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Expenses Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Branch</th>
                            <th class="text-end">Amount</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Added By</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            @php
                                $categoryColors = [
                                    'Rent' => 'primary',
                                    'Utilities' => 'info',
                                    'Salary' => 'success',
                                    'Transport' => 'warning',
                                    'Marketing' => 'purple',
                                    'Maintenance' => 'secondary',
                                    'Other' => 'dark',
                                ];
                                $catColor = $categoryColors[$expense->category] ?? 'secondary';
                            @endphp
                            <tr>
                                <td class="ps-3">{{ $expenses->firstItem() + $loop->index }}</td>
                                <td>
                                    <strong>{{ $expense->title }}</strong>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $catColor }}-subtle text-{{ $catColor }} border border-{{ $catColor }}-subtle">
                                        {{ $expense->category }}
                                    </span>
                                </td>
                                <td>
                                    @if ($expense->branch)
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="ti ti-building-store me-1"></i>{{ $expense->branch->name }}
                                        </span>
                                    @else
                                        <span class="text-muted small">Head Office</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong class="text-danger">৳{{ number_format($expense->amount, 2) }}</strong>
                                </td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block text-muted small" style="max-width:120px;"
                                        title="{{ $expense->description }}">
                                        {{ $expense->description ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ optional($expense->createdBy)->name ?? '—' }}</small>
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-soft-warning me-1" title="Edit"
                                        onclick="openEditModal(
                                            {{ $expense->id }},
                                            '{{ addslashes($expense->title) }}',
                                            '{{ $expense->amount }}',
                                            '{{ $expense->category }}',
                                            '{{ $expense->expense_date }}',
                                            '{{ $expense->branch_id ?? '' }}',
                                            '{{ addslashes($expense->description ?? '') }}'
                                        )">
                                        <i class="ti ti-pencil"></i>
                                    </button>
                                    <form action="{{ route('company.expenses.destroy', $expense->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete expense: {{ addslashes($expense->title) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="ti ti-receipt-off d-block mb-3" style="font-size:3rem;opacity:0.4;"></i>
                                    <h5 class="fw-semibold">No Expenses Found</h5>
                                    @if (request()->hasAny(['date_from', 'date_to', 'branch_id']))
                                        <p class="small mb-2">No results match your filter criteria.</p>
                                        <a href="{{ route('company.expenses.index') }}"
                                            class="btn btn-sm btn-outline-secondary">Clear Filters</a>
                                    @else
                                        <p class="small mb-3">No expenses have been recorded yet.</p>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#expenseModal" onclick="openAddModal()">
                                            <i class="ti ti-plus me-1"></i> Record First Expense
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($expenses->hasPages())
                <div class="px-3 py-2 d-flex justify-content-between align-items-center border-top">
                    <div class="text-muted small">
                        Showing {{ $expenses->firstItem() }} to {{ $expenses->lastItem() }}
                        of {{ $expenses->total() }} expenses
                    </div>
                    {{ $expenses->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Add / Edit Expense Modal --}}
    <div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="expenseForm" method="POST" action="{{ route('company.expenses.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="modal-header">
                        <h5 class="modal-title" id="expenseModalLabel">
                            <i class="ti ti-receipt-2 me-2"></i>Add New Expense
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="expenseTitle" class="form-control"
                                    placeholder="e.g., Office Rent - July 2026" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Amount (৳) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="amount" id="expenseAmount" class="form-control"
                                    step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select name="category" id="expenseCategory" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <option value="Rent">Rent</option>
                                    <option value="Utilities">Utilities</option>
                                    <option value="Salary">Salary</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                                <input type="date" name="expense_date" id="expenseDate" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Branch (Optional)</label>
                                <select name="branch_id" id="expenseBranch" class="form-select">
                                    <option value="">Head Office / General</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" id="expenseDescription" class="form-control" rows="2"
                                    placeholder="Additional details about this expense..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="ti ti-device-floppy me-1"></i> Save Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const storeUrl = "{{ route('company.expenses.store') }}";

        function openAddModal() {
            document.getElementById('expenseModalLabel').innerHTML =
                '<i class="ti ti-receipt-2 me-2"></i>Add New Expense';
            document.getElementById('expenseForm').action = storeUrl;
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('expenseTitle').value = '';
            document.getElementById('expenseAmount').value = '';
            document.getElementById('expenseCategory').value = '';
            document.getElementById('expenseDate').value = '{{ date('Y-m-d') }}';
            document.getElementById('expenseBranch').value = '';
            document.getElementById('expenseDescription').value = '';
            document.getElementById('submitBtn').innerHTML =
                '<i class="ti ti-device-floppy me-1"></i> Save Expense';
        }

        function openEditModal(id, title, amount, category, date, branchId, description) {
            const updateUrl = "{{ url('company/expenses') }}/" + id;
            document.getElementById('expenseModalLabel').innerHTML =
                '<i class="ti ti-pencil me-2"></i>Edit Expense';
            document.getElementById('expenseForm').action = updateUrl;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('expenseTitle').value = title;
            document.getElementById('expenseAmount').value = amount;
            document.getElementById('expenseCategory').value = category;
            document.getElementById('expenseDate').value = date;
            document.getElementById('expenseBranch').value = branchId;
            document.getElementById('expenseDescription').value = description;
            document.getElementById('submitBtn').innerHTML =
                '<i class="ti ti-device-floppy me-1"></i> Update Expense';

            const modal = new bootstrap.Modal(document.getElementById('expenseModal'));
            modal.show();
        }
    </script>
@endpush
