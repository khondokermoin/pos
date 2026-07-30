@extends('layouts.admin_master') 

@section('title', 'Receive & Sort Bulk Items')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Receive & Sort Bulk Items</h4>
            <p class="text-muted mb-0">Convert bulk/lot inventory into specific retail products for this branch.</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('branch.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('branch.inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Receive & Sort</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('branch.inventory.sorting-history') }}" class="btn btn-outline-primary">
            <i class="ti ti-history me-1"></i> Sorting History
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Sorting Form Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0"><i class="ti ti-box-multiple me-2 text-primary"></i>New Sorting Entry</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('branch.inventory.sort-items') }}" method="POST" id="sortingForm">
                @csrf

                <div class="row g-3 mb-4">
                    <!-- 1. Select Bulk Product -->
                    <div class="col-md-6">
                        <label for="bulk_product_id" class="form-label fw-semibold">Select Bulk Item / Lot <span class="text-danger">*</span></label>
                        <select name="bulk_product_id" id="bulk_product_id" class="form-select @error('bulk_product_id') is-invalid @enderror" required>
                            <option value="">-- Choose Bulk Item --</option>
                            @foreach($bulkProducts as $product)
                                <option value="{{ $product->id }}" data-stock="{{ $product->stock_quantity }}">
                                    {{ $product->name }} (Available: {{ $product->stock_quantity }} {{ $product->unit ?? 'pcs' }})
                                </option>
                            @endforeach
                        </select>
                        @error('bulk_product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 2. Quantity to Sort -->
                    <div class="col-md-3">
                        <label for="bulk_quantity_received" class="form-label fw-semibold">Qty to Sort <span class="text-danger">*</span></label>
                        <input type="number" name="bulk_quantity_received" id="bulk_quantity_received" 
                               class="form-control @error('bulk_quantity_received') is-invalid @enderror" 
                               min="1" placeholder="e.g., 10000" required>
                        @error('bulk_quantity_received')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 3. Reference Number -->
                    <div class="col-md-3">
                        <label for="reference_number" class="form-label fw-semibold">Reference (Optional)</label>
                        <input type="text" name="reference_number" id="reference_number" 
                               class="form-control" placeholder="Transfer ID / Invoice No.">
                    </div>
                </div>

                <hr class="my-4">

                <!-- 4. Dynamic Sorted Items Section -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-semibold"><i class="ti ti-list-check me-2"></i>Convert Into Retail Items</h6>
                    <button type="button" class="btn btn-sm btn-success" onclick="addSortedRow()">
                        <i class="ti ti-plus me-1"></i> Add Item Row
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="sortedItemsTable">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="45%">Retail Product <span class="text-danger">*</span></th>
                                <th width="20%">Quantity <span class="text-danger">*</span></th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="sortedItemsBody">
                            <!-- Rows will be added here dynamically -->
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="2" class="text-end">Total Sorted Quantity:</td>
                                <td>
                                    <span id="totalSortedQty" class="badge bg-secondary fs-6">0</span>
                                </td>
                                <td></td>
                            </tr>
                            <tr id="matchStatusRow" class="d-none">
                                <td colspan="2" class="text-end">Match Status:</td>
                                <td>
                                    <span id="matchStatus" class="badge fs-6">-</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Hidden input to store JSON data for backend -->
                <input type="hidden" name="sorted_items" id="sorted_items_json">

                <!-- Notes -->
                <div class="mb-4 mt-3">
                    <label for="notes" class="form-label fw-semibold">Notes / Remarks</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Any specific notes about this sorting..."></textarea>
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('branch.inventory.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="ti ti-check me-1"></i> Save & Convert Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Dynamic Rows and Validation -->
<script>
    // Retail products data passed from controller (Assuming $retailProducts is passed)
    const retailProducts = @json($retailProducts ?? []);
    let rowCount = 0;

    // Function to add a new row
    function addSortedRow() {
        rowCount++;
        const tbody = document.getElementById('sortedItemsBody');
        
        let productOptions = '<option value="">-- Select Product --</option>';
        retailProducts.forEach(product => {
            productOptions += `<option value="${product.id}">${product.name} (${product.category_name || 'No Category'})</option>`;
        });

        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="text-center">${rowCount}</td>
            <td>
                <select name="sorted_items[${rowCount}][product_id]" class="form-select retail-product-select" required onchange="updateTotals()">
                    ${productOptions}
                </select>
                <input type="hidden" name="sorted_items[${rowCount}][product_type]" value="retail">
            </td>
            <td>
                <input type="number" name="sorted_items[${rowCount}][quantity]" class="form-control sorted-qty" min="1" placeholder="0" required oninput="updateTotals()">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">
                    <i class="ti ti-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        updateTotals();
    }

    // Function to remove a row
    function removeRow(button) {
        const row = button.closest('tr');
        row.remove();
        renumberRows();
        updateTotals();
    }

    // Renumber rows after deletion
    function renumberRows() {
        const rows = document.querySelectorAll('#sortedItemsBody tr');
        rowCount = 0;
        rows.forEach((row, index) => {
            rowCount = index + 1;
            row.cells[0].textContent = rowCount;
            // Update input names to keep array sequential
            const select = row.querySelector('.retail-product-select');
            const qtyInput = row.querySelector('.sorted-qty');
            select.name = `sorted_items[${rowCount}][product_id]`;
            qtyInput.name = `sorted_items[${rowCount}][quantity]`;
        });
    }

    // Function to calculate totals and validate
    function updateTotals() {
        const bulkQtyInput = document.getElementById('bulk_quantity_received');
        const bulkQty = parseInt(bulkQtyInput.value) || 0;
        
        let totalSorted = 0;
        const qtyInputs = document.querySelectorAll('.sorted-qty');
        qtyInputs.forEach(input => {
            totalSorted += parseInt(input.value) || 0;
        });

        // Update UI
        document.getElementById('totalSortedQty').textContent = totalSorted;
        document.getElementById('matchStatusRow').classList.remove('d-none');
        
        const matchStatus = document.getElementById('matchStatus');
        const submitBtn = document.getElementById('submitBtn');

        if (bulkQty === 0) {
            matchStatus.className = 'badge bg-secondary fs-6';
            matchStatus.textContent = 'Enter Bulk Qty';
            submitBtn.disabled = true;
        } else if (totalSorted === bulkQty) {
            matchStatus.className = 'badge bg-success fs-6';
            matchStatus.textContent = '✓ Perfect Match';
            submitBtn.disabled = false;
        } else if (totalSorted < bulkQty) {
            matchStatus.className = 'badge bg-warning text-dark fs-6';
            matchStatus.textContent = `⚠ ${bulkQty - totalSorted} Remaining`;
            submitBtn.disabled = true;
        } else {
            matchStatus.className = 'badge bg-danger fs-6';
            matchStatus.textContent = `✗ Exceeded by ${totalSorted - bulkQty}`;
            submitBtn.disabled = true;
        }

        // Update hidden JSON input for backend (optional, since Laravel handles array natively, but good for backup)
        // Laravel will automatically parse the sorted_items[name][field] format.
    }

    // Add first row on load
    document.addEventListener('DOMContentLoaded', () => {
        if (retailProducts.length > 0) {
            addSortedRow();
        } else {
            document.getElementById('sortedItemsBody').innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        No retail products found. Please create retail products first.
                    </td>
                </tr>
            `;
            document.getElementById('submitBtn').disabled = true;
        }
    });

    // Form submission double-check
    document.getElementById('sortingForm').addEventListener('submit', function(e) {
        const bulkQty = parseInt(document.getElementById('bulk_quantity_received').value) || 0;
        let totalSorted = 0;
        document.querySelectorAll('.sorted-qty').forEach(input => {
            totalSorted += parseInt(input.value) || 0;
        });

        if (totalSorted !== bulkQty) {
            e.preventDefault();
            alert('Error: Total sorted quantity must exactly match the bulk quantity received!');
        }
    });
</script>
@endsection