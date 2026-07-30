@extends('layouts.admin_master')

@section('title', 'Products')

@section('content')
    <div class="mb-2 row">
        <div class="col-sm-6">
            <h4 class="page-title">Products</h4>
            <p class="text-muted mb-0">Manage your catalog, inventory, and product variants from one professional dashboard.</p>
        </div>
        <div class="col-sm-6 text-sm-end">
            <nav aria-label="breadcrumb">
                <ol class="mb-0 breadcrumb justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Products</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h5 class="card-title mb-0">Product Catalog</h5>
                            <p class="text-muted mb-0">All products created for your company.</p>
                        </div>
                        <a href="{{ route('company.products.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i> Add Product
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-top-0">Product</th>
                                    <th class="border-top-0">Category / Brand</th>
                                    <th class="border-top-0">Price</th>
                                    <th class="border-top-0">Stock</th>
                                    <th class="border-top-0">Status</th>
                                    <th class="border-top-0 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    @php
                                        $totalStock = $product->variants->sum(fn($variant) => optional($variant->stock)->quantity ?: 0);
                                        $lowStock = $product->variants->contains(fn($variant) => optional($variant->stock)->quantity <= optional($variant)->reorder_level);
                                        $displaySku = optional($product->variants->first())->sku ?? 'N/A';
                                        $displayPrice = optional($product->variants->first())->selling_price ? number_format(optional($product->variants->first())->selling_price, 2) : '0.00';
                                        $displayCost = optional($product->variants->first())->cost_price ? number_format(optional($product->variants->first())->cost_price, 2) : '0.00';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-sm rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center">
                                                    @if($product->image)
                                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="avatar-xs rounded-circle">
                                                    @else
                                                        <span class="fs-5 text-primary">{{ strtoupper(substr($product->name, 0, 1)) }}</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $product->name }}</h6>
                                                    <span class="text-muted">SKU: {{ $displaySku }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ optional($product->category)->name ?? 'Uncategorized' }}</span>
                                                <small class="text-muted">{{ optional($product->brand)->name ?? 'No Brand' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold">৳ {{ $displayPrice }}</span>
                                                <small class="text-muted">Cost: ৳ {{ $displayCost }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $lowStock ? 'bg-danger text-white' : 'bg-success text-white' }}">
                                                {{ $totalStock }} pcs
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('company.products.show', $product) }}" class="btn btn-sm btn-soft-primary me-1" title="View">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('company.products.edit', $product) }}" class="btn btn-sm btn-soft-warning me-1" title="Edit">
                                                <i class="ti ti-pencil"></i>
                                            </a>
                                            <form action="{{ route('company.products.destroy', $product) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('Are you sure you want to delete this product?');" title="Delete">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No products found. Start by adding your first product.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
