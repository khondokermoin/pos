@extends('layouts.admin_master')

@section('title', 'Stock Adjustment')

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0">
                <i class="ti ti-adjustments me-2 text-primary"></i>Stock Adjustment
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Stock Adjustment</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('company.inventory.low-stock') }}" class="btn btn-outline-danger">
                <i class="ti ti-alert-triangle me-1"></i> View Low Stock Alerts
            </a>
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

    <div class="row">
        {{-- Left: Adjustment Form --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-edit me-2"></i>Apply Adjustment</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('company.inventory.stock-adjust.store') }}" method="POST">
                        @csrf

                        {{-- Branch Select --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Branch / Location</label>
                            <select name="branch_id" id="branchSelect"
                                class="form-select @error('branch_id') is-invalid @enderror">
                                <option value="">🏢 Central Warehouse / Head Office</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Product Variant Select --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Product / Variant <span
                                    class="text-danger">*</span></label>
                            <select name="variant_id" id="variantSelect"
                                class="form-select @error('variant_id') is-invalid @enderror" required
                                onchange="updateCurrentStock()">
                                <option value="">Select Product Variant</option>
                                @foreach ($variants as $variant)
                                    <option value="{{ $variant->id }}"
                                        data-stock="{{ optional($variant->stock)->quantity ?? 0 }}"
                                        {{ old('variant_id') == $variant->id ? 'selected' : '' }}>
                                        {{ optional($variant->product)->name }} — {{ $variant->sku }}
                                    </option>
                                @endforeach
                            </select>
                            @error('variant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            {{-- Current Stock Display --}}
                            <div id="currentStockDisplay" class="mt-2" style="display:none;">
                                <span class="badge bg-info-subtle text-info px-3 py-2">
                                    <i class="ti ti-package me-1"></i>
                                    Current Stock: <strong id="currentStockValue">0</strong> pcs
                                </span>
                            </div>
                        </div>

                        {{-- Adjustment Type --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Adjustment Type <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="typeAdd"
                                        value="add" {{ old('type', 'add') === 'add' ? 'checked' : '' }} required>
                                    <label class="form-check-label text-success fw-semibold" for="typeAdd">
                                        <i class="ti ti-plus me-1"></i> Add Stock
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="typeSubtract"
                                        value="subtract" {{ old('type') === 'subtract' ? 'checked' : '' }}>
                                    <label class="form-check-label text-danger fw-semibold" for="typeSubtract">
                                        <i class="ti ti-minus me-1"></i> Subtract Stock
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="typeSet"
                                        value="set" {{ old('type') === 'set' ? 'checked' : '' }}>
                                    <label class="form-check-label text-primary fw-semibold" for="typeSet">
                                        <i class="ti ti-equal me-1"></i> Set Exact
                                    </label>
                                </div>
                            </div>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Quantity --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantityInput"
                                class="form-control @error('quantity') is-invalid @enderror"
                                value="{{ old('quantity', 1) }}" min="1" required placeholder="Enter quantity">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Reason --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Reason / Notes</label>
                            <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="3"
                                placeholder="e.g., Damaged goods, Physical count correction, Return from customer...">{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-check me-1"></i> Apply Adjustment
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Recent Adjustments --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="ti ti-history me-2"></i>Recent Adjustments</h5>
                    <span class="badge bg-secondary">Last 10</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Date</th>
                                    <th>Product</th>
                                    <th>Branch</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Qty</th>
                                    <th>Reason</th>
                                    <th class="pe-3">By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAdjustments as $adj)
                                    <tr>
                                        <td class="ps-3">
                                            <small class="text-muted">
                                                {{ $adj->created_at->format('d M') }}<br>
                                                {{ $adj->created_at->format('h:i A') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold small">
                                                {{ optional(optional($adj->variant)->product)->name ?? '—' }}
                                            </div>
                                            <code class="small">{{ optional($adj->variant)->sku ?? '' }}</code>
                                        </td>
                                        <td>
                                            @if ($adj->branch)
                                                <span class="badge bg-info-subtle text-info small">
                                                    {{ $adj->branch->name }}
                                                </span>
                                            @else
                                                <span class="text-muted small">Central</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                // Parse type from reference string
                                                $refLower = strtolower($adj->reference ?? '');
                                                $adjType = str_contains($refLower, 'before')
                                                    ? 'adjustment'
                                                    : $adj->type;
                                            @endphp
                                            <span class="badge bg-primary-subtle text-primary">
                                                {{ ucfirst($adj->type) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $adj->quantity }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted text-truncate d-inline-block"
                                                style="max-width:120px;" title="{{ $adj->reference }}">
                                                {{ $adj->reference ?? '—' }}
                                            </small>
                                        </td>
                                        <td class="pe-3">
                                            <small>{{ optional($adj->user)->name ?? '—' }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="ti ti-history d-block mb-2" style="font-size:2rem;opacity:0.4;"></i>
                                            No adjustments recorded yet.
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
@endsection

@push('scripts')
    <script>
        // Pass variants data from PHP to JS for current stock display
        const variantsData = @json(
            $variants->map(function ($v) {
                return [
                    'id' => $v->id,
                    'stock' => optional($v->stock)->quantity ?? 0,
                ];
            })
        );

        function updateCurrentStock() {
            const select = document.getElementById('variantSelect');
            const selectedOption = select.options[select.selectedIndex];
            const stockDisplay = document.getElementById('currentStockDisplay');
            const stockValue = document.getElementById('currentStockValue');

            if (select.value) {
                const stock = selectedOption.getAttribute('data-stock') || 0;
                stockValue.textContent = stock;
                stockDisplay.style.display = 'block';
            } else {
                stockDisplay.style.display = 'none';
            }
        }

        // Run on page load if old value is selected
        document.addEventListener('DOMContentLoaded', function() {
            updateCurrentStock();
        });
    </script>
@endpush
