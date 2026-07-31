@extends('layouts.admin_master')

@section('title', 'New Stock Transfer')

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-transfer me-2 text-primary"></i>New Stock Transfer</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('company.transfers.index') }}">Transfers</a></li>
                    <li class="breadcrumb-item active">New Transfer</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('company.transfers.index') }}" class="btn btn-light">
                <i class="ti ti-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('company.transfers.store') }}" method="POST" id="transferForm">
        @csrf

        <div class="row">
            {{-- Left: Transfer Details --}}
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ti ti-settings me-2"></i>Transfer Details</h5>
                    </div>
                    <div class="card-body">

                        {{-- From Branch --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">From (Source)</label>
                            <select name="from_branch_id" id="fromBranch"
                                class="form-select @error('from_branch_id') is-invalid @enderror">
                                <option value="">🏢 Central Warehouse / Head Office</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('from_branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('from_branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave blank to transfer from Central Warehouse.</small>
                        </div>

                        {{-- Arrow indicator --}}
                        <div class="text-center my-2">
                            <i class="ti ti-arrow-down fs-3 text-primary"></i>
                        </div>

                        {{-- To Branch --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">To (Destination) <span
                                    class="text-danger">*</span></label>
                            <select name="to_branch_id" id="toBranch"
                                class="form-select @error('to_branch_id') is-invalid @enderror" required>
                                <option value="">Select Destination Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('to_branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('to_branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Transfer Date --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Transfer Date <span class="text-danger">*</span></label>
                            <input type="date" name="transfer_date"
                                class="form-control @error('transfer_date') is-invalid @enderror"
                                value="{{ old('transfer_date', date('Y-m-d')) }}" required>
                            @error('transfer_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes (Optional)</label>
                            <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="3"
                                placeholder="Reason for transfer, special instructions...">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Submit --}}
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-warning py-2 mb-3">
                            <i class="ti ti-alert-triangle me-2"></i>
                            <small><strong>Warning:</strong> This action immediately deducts stock from the source and adds
                                to the destination. It cannot be undone.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                            <i class="ti ti-transfer me-1"></i> Execute Transfer
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right: Items Table --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="ti ti-list me-2"></i>Transfer Items</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItemRow()">
                            <i class="ti ti-plus me-1"></i> Add Item
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="45%">Product / Variant</th>
                                        <th width="20%">Current Stock</th>
                                        <th width="25%">Qty to Transfer</th>
                                        <th width="10%"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    {{-- Rows added by JS --}}
                                </tbody>
                            </table>
                        </div>

                        <div class="p-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Add at least 1 item to proceed.
                                </span>
                                <span class="badge bg-primary-subtle text-primary" id="itemCountBadge">
                                    0 item(s)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @error('items')
                    <div class="alert alert-danger mt-2">
                        <i class="ti ti-alert-circle me-2"></i>{{ $message }}
                    </div>
                @enderror
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        // Pass variants data from PHP to JS
        @php
            $variantsJson = $variants->map(function ($v) {
                return [
                    'id' => $v->id,
                    'label' => optional($v->product)->name . ' — ' . $v->sku,
                    'stock' => optional($v->stock)->quantity ?? 0,
                    'sku' => $v->sku,
                ];
            });
        @endphp
        const variantsData = @json($variantsJson);

        let rowCount = 0;

        function buildVariantOptions(selectedId = '') {
            let opts = '<option value="">Select Product Variant</option>';
            variantsData.forEach(v => {
                const sel = v.id == selectedId ? 'selected' : '';
                opts +=
                    `<option value="${v.id}" data-stock="${v.stock}" ${sel}>${v.label} (Stock: ${v.stock})</option>`;
            });
            return opts;
        }

        function addItemRow(variantId = '', qty = 1) {
            const tbody = document.getElementById('itemsBody');
            const idx = rowCount++;

            const row = document.createElement('tr');
            row.id = `row-${idx}`;
            row.innerHTML = `
                <td>
                    <select name="items[${idx}][variant_id]" class="form-select form-select-sm variant-select"
                        required onchange="updateStock(this)">
                        ${buildVariantOptions(variantId)}
                    </select>
                </td>
                <td>
                    <span class="badge bg-info-subtle text-info px-3 py-2 current-stock-badge">
                        — pcs
                    </span>
                </td>
                <td>
                    <input type="number" name="items[${idx}][quantity]"
                        class="form-control form-control-sm qty-input"
                        value="${qty}" min="1" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(${idx})">
                        <i class="ti ti-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);

            // If pre-selected, update stock display
            if (variantId) {
                const select = row.querySelector('.variant-select');
                updateStock(select);
            }

            updateItemCount();
        }

        function updateStock(selectEl) {
            const row = selectEl.closest('tr');
            const badge = row.querySelector('.current-stock-badge');
            const stock = selectEl.options[selectEl.selectedIndex]?.getAttribute('data-stock') ?? '—';
            badge.textContent = stock + ' pcs';

            // Color code based on stock level
            badge.className = 'badge px-3 py-2 current-stock-badge';
            if (stock === '—' || stock === null) {
                badge.classList.add('bg-secondary-subtle', 'text-secondary');
            } else if (parseInt(stock) === 0) {
                badge.classList.add('bg-danger-subtle', 'text-danger');
            } else if (parseInt(stock) <= 5) {
                badge.classList.add('bg-warning-subtle', 'text-warning');
            } else {
                badge.classList.add('bg-success-subtle', 'text-success');
            }
        }

        function removeRow(idx) {
            const row = document.getElementById(`row-${idx}`);
            if (row) row.remove();
            updateItemCount();
        }

        function updateItemCount() {
            const count = document.getElementById('itemsBody').children.length;
            document.getElementById('itemCountBadge').textContent = count + ' item(s)';
        }

        // Validate same source/destination before submit
        document.getElementById('transferForm').addEventListener('submit', function(e) {
            const from = document.getElementById('fromBranch').value;
            const to = document.getElementById('toBranch').value;

            if (from && from === to) {
                e.preventDefault();
                alert('Source and destination branch cannot be the same!');
                return false;
            }

            const itemCount = document.getElementById('itemsBody').children.length;
            if (itemCount === 0) {
                e.preventDefault();
                alert('Please add at least one item to transfer.');
                return false;
            }
        });

        // Add first row on page load
        document.addEventListener('DOMContentLoaded', function() {
            addItemRow();
        });
    </script>
@endpush
