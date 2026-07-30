@extends('layouts.admin_master')

@section('content')
<div class="container-fluid">
    <div class="page-title-box">
        <h4 class="page-title">Sale Details</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Invoice {{ $sale->invoice_no ?? '#'.$sale->id }}</h5>
            <p><strong>Customer:</strong> {{ $sale->customer->name ?? 'Walk-in' }}</p>
            <p><strong>Total:</strong> ৳{{ number_format($sale->total_amount ?? 0, 2) }}</p>
            <p><strong>Date:</strong> {{ $sale->created_at->format('d M Y, h:i A') }}</p>

            <table class="table table-bordered mt-3">
                <thead>
                    <tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    <tr>
                        <td>{{ $item->variant->product->name ?? $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>৳{{ number_format($item->unit_price, 2) }}</td>
                        <td>৳{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
