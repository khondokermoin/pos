@extends('layouts.admin_master')
@section('content')
<div class="container-fluid">
    <div class="page-title-box"><h4 class="page-title">Daily Sales Report</h4></div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4"><label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}"></div>
                <div class="col-md-2"><button type="submit" class="btn btn-primary w-100 mt-4">Filter</button></div>
            </form>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6"><div class="card bg-primary text-white"><div class="card-body text-center">
            <h6 class="text-white-50">Total Sales</h6><h2>{{ $totalSales }}</h2>
        </div></div></div>
        <div class="col-md-6"><div class="card bg-success text-white"><div class="card-body text-center">
            <h6 class="text-white-50">Total Revenue</h6><h2>৳{{ number_format($totalRevenue, 2) }}</h2>
        </div></div></div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Invoice</th><th>Customer</th><th>Total</th><th>Time</th></tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td><code>{{ $sale->invoice_no ?? '#'.$sale->id }}</code></td>
                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td><strong>৳{{ number_format($sale->total_amount ?? 0, 2) }}</strong></td>
                            <td>{{ $sale->created_at->format('h:i A') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No sales on this date.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
