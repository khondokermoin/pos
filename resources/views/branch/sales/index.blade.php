@extends('layouts.admin_master')

@section('content')
    <div class="container-fluid">
        <div class="page-title-box">
            <h4 class="page-title">Branch Sales</h4>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td><code>{{ $sale->invoice_no ?? '#' . $sale->id }}</code></td>
                                    <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                    <td>{{ $sale->items_count ?? 0 }}</td>
                                    <td><strong>৳{{ number_format($sale->total_amount ?? 0, 2) }}</strong></td>
                                    <td>{{ $sale->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No sales found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $sales->links() }}</div>
            </div>
        </div>
    </div>
@endsection
