@extends('layouts.admin_master')
@section('title', 'New Purchase')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">New Purchase (Stock In)</h4>
                    <a href="{{ route('company.purchases.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('company.purchases.store') }}" method="POST" id="purchaseForm">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Target Branch <span class="text-danger">*</span></label>
                                <select name="branch_id" class="form-select" required>
                                    <option value="">Select Branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" class="form-select" required>
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" name="purchase_date" class="form-control"
                                    value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <hr>
                        <h5 class="mb-3">Purchase Items</h5>

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Product / Variant</th>
                                        <th width="20%">Quantity</th>
                                        <th width="20%">Unit Price (৳)</th>
                                        <th width="15%">Subtotal (৳)</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    <!-- Rows will be added here by JS -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total Amount:</td>
                                        <td class="fw-bold">৳<span id="grandTotal">0.00</span></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <input type="hidden" name="total_amount" id="totalAmountInput" value="0">

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary" onclick="addItemRow()">
                                <i class="ti ti-plus"></i> Add Item
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-check"></i> Save Purchase & Update Stock
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // PHP থেকে JS এ variants ডাটা পাস করা
            const variantsData = @json($variants);

            function addItemRow() {
                const tbody = document.getElementById('itemsBody');
                const rowIndex = tbody.children.length;

                let options = '<option value="">Select Product Variant</option>';
                variantsData.forEach(v => {
                    options +=
                        `<option value="${v.id}" data-price="${v.cost_price || v.selling_price}">${v.product.name} - ${v.sku} (${v.name || 'Default'})</option>`;
                });

                const row = document.createElement('tr');
                row.innerHTML = `
            <td>
                <select name="items[${rowIndex}][variant_id]" class="form-select variant-select" required onchange="updatePrice(this)">
                    ${options}
                </select>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][quantity]" class="form-control qty-input" value="1" min="1" required oninput="calculateSubtotal(this)">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][unit_price]" class="form-control price-input" value="0" step="0.01" min="0" required oninput="calculateSubtotal(this)">
            </td>
            <td>
                <span class="subtotal-display">0.00</span>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">
                    <i class="ti ti-trash"></i>
                </button>
            </td>
        `;
                tbody.appendChild(row);
            }

            function updatePrice(selectElement) {
                const price = selectElement.options[selectElement.selectedIndex].getAttribute('data-price') || 0;
                const row = selectElement.closest('tr');
                row.querySelector('.price-input').value = price;
                calculateSubtotal(row.querySelector('.qty-input'));
            }

            function calculateSubtotal(inputElement) {
                const row = inputElement.closest('tr');
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                const subtotal = qty * price;

                row.querySelector('.subtotal-display').innerText = subtotal.toFixed(2);
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let grandTotal = 0;
                document.querySelectorAll('.subtotal-display').forEach(el => {
                    grandTotal += parseFloat(el.innerText) || 0;
                });
                document.getElementById('grandTotal').innerText = grandTotal.toFixed(2);
                document.getElementById('totalAmountInput').value = grandTotal.toFixed(2);
            }

            function removeRow(btn) {
                btn.closest('tr').remove();
                calculateGrandTotal();
            }

            // পেজ লোড হলে প্রথম রো অটো যোগ হবে
            document.addEventListener('DOMContentLoaded', () => {
                addItemRow();
            });
        </script>
    @endpush
@endsection
