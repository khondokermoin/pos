@extends('layouts.admin_master')
@section('content')
<div class="container-fluid">
    <div class="page-title-box"><h4 class="page-title">Stock Report</h4></div>

    <div class="row mb-3">
        <div class="col-md-6"><div class="card bg-warning text-white"><div class="card-body text-center">
            <h6 class="text-white-50">Low Stock (≤5)</h6><h2>{{ $lowStockCount }}</h2>
        </div></div></div>
        <div class="col-md-6"><div class="card bg-danger text-white"><div class="card-body text-center">
            <h6 class="text-white-50">Out of Stock</h6><h2>{{ $outOfStockCount }}</h2>
        </div></div></div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Branch</th><th>Quantity</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                        <tr>
                            <td><strong>{{ $stock->variant->product->name ?? 'N/A' }}</strong></td>
                            <td>{{ $stock->branch->name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $stock->quantity == 0 ? 'danger' : ($stock->quantity <= 5 ? 'warning' : 'success') }}">
                                    {{ $stock->quantity }}
                                </span>
                            </td>
                            <td>
                                @if($stock->quantity == 0) <span class="text-danger">Out of Stock</span>
                                @elseif($stock->quantity <= 5) <span class="text-warning">Low Stock</span>
                                @else <span class="text-success">In Stock</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No stock data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $stocks->withQueryString()->links() }}</div>
        </div>
    </div>
</div>
@endsection
