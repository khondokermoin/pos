@extends('layouts.admin_master')

@section('title', 'Add New Product')

@section('content')
    <div class="mb-2 row">
        <div class="col-sm-6">
            <h4 class="page-title">Add New Product</h4>
        </div>
        <div class="col-sm-6 text-sm-end">
            <nav aria-label="breadcrumb">
                <ol class="mb-0 breadcrumb justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('company.products.index') }}">Products</a></li>
                    <li class="breadcrumb-item active">Add Product</li>
                </ol>
            </nav>
        </div>
    </div>

    <form id="product-form" action="{{ route('company.products.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-12">

                {{-- ==========================================
                1. Basic Product Information
            ========================================== --}}
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3 header-title">Basic Information</h4>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Product Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}"
                                    placeholder="e.g., Premium Cotton T-Shirt" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="category_id" class="form-label">Category <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="brand_id" class="form-label">Brand</label>
                                <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id"
                                    name="brand_id">
                                    <option value="">Select Brand (Optional)</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label d-block pt-2">Product Type</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="has_variants" name="has_variants"
                                        value="1" {{ old('has_variants') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_variants">This product has variants (e.g.,
                                        Size, Color, Weight)</label>
                                </div>
                                <small class="text-muted">If checked, you will define specific SKUs, prices, and stock for
                                    each variant below.</small>
                            </div>

                            <div class="mb-3 col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3" placeholder="Product details...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==========================================
                2. Product Variants / Inventory Details
            ========================================== --}}
                <div class="mt-3 card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="header-title mb-0">Variant & Inventory Details</h4>
                            <button type="button" class="btn btn-sm btn-primary" id="add-variant-btn">
                                <i class="ti ti-plus me-1"></i> Add Variant
                            </button>
                        </div>

                        <div id="variants-container">
                            @php
                                // ভ্যালিডেশন এরর হলে আগের ডাটা রিস্টোর করা, না হলে একটি খালি ভেরিয়েন্ট দেখানো
                                $oldVariants = old('variants');
                                if (empty($oldVariants)) {
                                    $oldVariants = [
                                        [
                                            'sku' => '',
                                            'barcode' => '',
                                            'unit_id' => '',
                                            'tax_id' => '',
                                            'cost_price' => 0,
                                            'selling_price' => 0,
                                            'stock' => 0,
                                            'reorder_level' => 5,
                                            'attributes' => [],
                                        ],
                                    ];
                                }
                            @endphp

                            @foreach ($oldVariants as $index => $variant)
                                <div class="variant-item card bg-light mb-3" data-index="{{ $index }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">Variant #<span
                                                    class="variant-number">{{ $index + 1 }}</span></h5>
                                            @if ($index > 0 || count($oldVariants) > 1)
                                                <button type="button" class="btn btn-sm btn-danger remove-variant-btn">
                                                    <i class="ti ti-trash"></i> Remove
                                                </button>
                                            @endif
                                        </div>

                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">SKU <span class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('variants.' . $index . '.sku') is-invalid @enderror"
                                                    name="variants[{{ $index }}][sku]"
                                                    value="{{ $variant['sku'] ?? '' }}" required>
                                                @error('variants.' . $index . '.sku')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Barcode</label>
                                                <input type="text"
                                                    class="form-control @error('variants.' . $index . '.barcode') is-invalid @enderror"
                                                    name="variants[{{ $index }}][barcode]"
                                                    value="{{ $variant['barcode'] ?? '' }}">
                                                @error('variants.' . $index . '.barcode')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Unit <span class="text-danger">*</span></label>
                                                <select
                                                    class="form-select @error('variants.' . $index . '.unit_id') is-invalid @enderror"
                                                    name="variants[{{ $index }}][unit_id]" required>
                                                    <option value="">Select Unit</option>
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}"
                                                            {{ isset($variant['unit_id']) && $variant['unit_id'] == $unit->id ? 'selected' : '' }}>
                                                            {{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('variants.' . $index . '.unit_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Tax</label>
                                                <select
                                                    class="form-select @error('variants.' . $index . '.tax_id') is-invalid @enderror"
                                                    name="variants[{{ $index }}][tax_id]">
                                                    <option value="">No Tax</option>
                                                    @foreach ($taxes as $tax)
                                                        <option value="{{ $tax->id }}"
                                                            {{ isset($variant['tax_id']) && $variant['tax_id'] == $tax->id ? 'selected' : '' }}>
                                                            {{ $tax->name }} ({{ $tax->rate }}%)</option>
                                                    @endforeach
                                                </select>
                                                @error('variants.' . $index . '.tax_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Cost Price <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" step="0.01"
                                                    class="form-control @error('variants.' . $index . '.cost_price') is-invalid @enderror"
                                                    name="variants[{{ $index }}][cost_price]"
                                                    value="{{ $variant['cost_price'] ?? 0 }}" required>
                                                @error('variants.' . $index . '.cost_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Selling Price <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" step="0.01"
                                                    class="form-control @error('variants.' . $index . '.selling_price') is-invalid @enderror"
                                                    name="variants[{{ $index }}][selling_price]"
                                                    value="{{ $variant['selling_price'] ?? 0 }}" required>
                                                @error('variants.' . $index . '.selling_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Initial Stock <span
                                                        class="text-danger">*</span></label>
                                                <input type="number"
                                                    class="form-control @error('variants.' . $index . '.stock') is-invalid @enderror"
                                                    name="variants[{{ $index }}][stock]"
                                                    value="{{ $variant['stock'] ?? 0 }}" required>
                                                @error('variants.' . $index . '.stock')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Reorder Level</label>
                                                <input type="number"
                                                    class="form-control @error('variants.' . $index . '.reorder_level') is-invalid @enderror"
                                                    name="variants[{{ $index }}][reorder_level]"
                                                    value="{{ $variant['reorder_level'] ?? 5 }}">
                                                @error('variants.' . $index . '.reorder_level')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Attributes Section --}}
                                            <div class="col-12">
                                                <label class="form-label">Attributes (e.g., Color: Red, Size: M)</label>
                                                <div class="attributes-container"
                                                    data-variant-index="{{ $index }}">
                                                    @if (isset($variant['attributes']) && is_array($variant['attributes']))
                                                        @foreach ($variant['attributes'] as $attrIndex => $attrData)
                                                            @php
                                                                // Handle both associative array and indexed array formats from old input
                                                                $aKey = is_array($attrData)
                                                                    ? $attrData['key'] ?? ''
                                                                    : $attrIndex;
                                                                $aVal = is_array($attrData)
                                                                    ? $attrData['value'] ?? ''
                                                                    : $attrData;
                                                            @endphp
                                                            @if ($aKey || $aVal)
                                                                <div class="row mb-2 attribute-row">
                                                                    <div class="col-5">
                                                                        <input type="text"
                                                                            class="form-control form-control-sm"
                                                                            name="variants[{{ $index }}][attributes][{{ $attrIndex }}][key]"
                                                                            value="{{ $aKey }}"
                                                                            placeholder="Name (e.g., Color)">
                                                                    </div>
                                                                    <div class="col-5">
                                                                        <input type="text"
                                                                            class="form-control form-control-sm"
                                                                            name="variants[{{ $index }}][attributes][{{ $attrIndex }}][value]"
                                                                            value="{{ $aVal }}"
                                                                            placeholder="Value (e.g., Red)">
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-outline-danger w-100 remove-attribute-btn"><i
                                                                                class="ti ti-x"></i></button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-secondary mt-2 add-attribute-btn"
                                                    data-variant-index="{{ $index }}">
                                                    <i class="ti ti-plus"></i> Add Attribute
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @error('variants')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- ==========================================
                3. Action Buttons
            ========================================== --}}
                <div class="mt-3 card">
                    <div class="card-body text-end">
                        <a href="{{ route('company.products.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <i class="ti ti-device-floppy me-1"></i> Save Product
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>

    {{-- ==========================================
    JavaScript for Dynamic Variants & Attributes
========================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let variantCount = {{ count($oldVariants) }};

            // 1. Add New Variant
            document.getElementById('add-variant-btn').addEventListener('click', function() {
                const container = document.getElementById('variants-container');
                const newIndex = variantCount++;

                // Blade ডাটা থেকে ড্রপডাউন অপশন তৈরি করা
                const unitsOptions =
                    `@foreach ($units as $unit)<option value="{{ $unit->id }}">{{ $unit->name }}</option>@endforeach`;
                const taxesOptions =
                    `<option value="">No Tax</option>@foreach ($taxes as $tax)<option value="{{ $tax->id }}">{{ $tax->name }} ({{ $tax->rate }}%)</option>@endforeach`;

                const template = `
        <div class="variant-item card bg-light mb-3" data-index="${newIndex}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Variant #<span class="variant-number">${newIndex + 1}</span></h5>
                    <button type="button" class="btn btn-sm btn-danger remove-variant-btn"><i class="ti ti-trash"></i> Remove</button>
                </div>
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label">SKU <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="variants[${newIndex}][sku]" required>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Barcode</label>
                        <input type="text" class="form-control" name="variants[${newIndex}][barcode]">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Unit <span class="text-danger">*</span></label>
                        <select class="form-select" name="variants[${newIndex}][unit_id]" required>
                            <option value="">Select Unit</option>
                            ${unitsOptions}
                        </select>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Tax</label>
                        <select class="form-select" name="variants[${newIndex}][tax_id]">
                            ${taxesOptions}
                        </select>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label">Cost Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" name="variants[${newIndex}][cost_price]" value="0" required>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" name="variants[${newIndex}][selling_price]" value="0" required>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label">Initial Stock <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="variants[${newIndex}][stock]" value="0" required>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label">Reorder Level</label>
                        <input type="number" class="form-control" name="variants[${newIndex}][reorder_level]" value="5">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Attributes</label>
                        <div class="attributes-container" data-variant-index="${newIndex}"></div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2 add-attribute-btn" data-variant-index="${newIndex}">
                            <i class="ti ti-plus"></i> Add Attribute
                        </button>
                    </div>
                </div>
            </div>
        </div>`;

                container.insertAdjacentHTML('beforeend', template);
            });

            // 2. Event Delegation for Dynamic Elements (Remove Variant, Add/Remove Attribute)
            document.getElementById('variants-container').addEventListener('click', function(e) {

                // Remove Variant
                if (e.target.closest('.remove-variant-btn')) {
                    e.target.closest('.variant-item').remove();
                    // রিমুভ করার পর ভেরিয়েন্ট নম্বর আপডেট করা
                    document.querySelectorAll('.variant-item').forEach((item, idx) => {
                        item.querySelector('.variant-number').textContent = idx + 1;
                    });
                }

                // Add Attribute
                if (e.target.closest('.add-attribute-btn')) {
                    const vIndex = e.target.closest('.add-attribute-btn').dataset.variantIndex;
                    const container = document.querySelector(
                        `.attributes-container[data-variant-index="${vIndex}"]`);
                    const attrIndex = container.children.length;

                    const attrHtml = `
                <div class="row mb-2 attribute-row">
                    <div class="col-5">
                        <input type="text" class="form-control form-control-sm" name="variants[${vIndex}][attributes][${attrIndex}][key]" placeholder="Name (e.g., Color)">
                    </div>
                    <div class="col-5">
                        <input type="text" class="form-control form-control-sm" name="variants[${vIndex}][attributes][${attrIndex}][value]" placeholder="Value (e.g., Red)">
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-sm btn-outline-danger w-100 remove-attribute-btn"><i class="ti ti-x"></i></button>
                    </div>
                </div>
            `;
                    container.insertAdjacentHTML('beforeend', attrHtml);
                }

                // Remove Attribute
                if (e.target.closest('.remove-attribute-btn')) {
                    e.target.closest('.attribute-row').remove();
                }
            });
        });
        // Add Product Variants Toggle Logic
        const hasVariantsCheckbox = document.getElementById('has_variants');
        const variantsContainer = document.getElementById('variants-container');
        const addVariantBtn = document.getElementById('add-variant-btn');

        function toggleVariants() {
            if (hasVariantsCheckbox.checked) {
                variantsContainer.style.display = 'block';
                if (addVariantBtn) addVariantBtn.style.display = 'inline-block';
            } else {
                variantsContainer.style.display = 'none';
                if (addVariantBtn) addVariantBtn.style.display = 'none';
            }
        }

        hasVariantsCheckbox.addEventListener('change', toggleVariants);
        toggleVariants(); // পেজ লোড হওয়ার সময় চেক করবে
    </script>
@endsection
