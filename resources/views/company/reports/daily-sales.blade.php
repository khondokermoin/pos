@extends('layouts.admin_master')
@section('content')
<div class="container-fluid">
    <div class="page-title-box"><h4 class="page-title">Sales Report</h4></div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3"><label class="form-label">From</label>
                    <input type="date" name="from" class="form-control" value="{{ $from }}"></div>
                <div class="col-md-3"><label class="form-label">To</label>
                    <input type="date" name="to" class="form-control" value="{{ $to }}"></div>
                <div class="col-md-2"><button type="submit" class="btn btn-primary w-100 mt-4">Filter</button></div>
                <div class="col-md-2"><a href="{{ route('company.reports.sales') }}" class="btn btn-outline-secondary w-100 mt-4">Reset</a></div>
            </form>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4"><div class="card bg-primary text-white"><div class="card-body text-center">
            <h6 class="text-white-50">Total Sales</h6><h2>{{ $totalSales }}</h2>
        </div></div></div>
        <div class="col-md-4"><div class="card bg-success text-white"><div class="card-body text-center">
            <h6 class="text-white-50">Total Revenue</h6><h2>৳{{ number_format($totalRevenue, 2) }}</h2>
        </div></div></div>
        <div class="col-md-4"><div class="card bg-warning text-white"><div class="card-body text-center">
            <h6 class="text-white-50">Total Discount</h6><h2>৳{{ number_format($totalDiscount, 2) }}</h2>
        </div></div></div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Invoice</th><th>Branch</th><th>Customer</th><th>Total</th><th>Discount</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td><code>{{ $sale->invoice_no ?? '#'.$sale->id }}</code></td>
                            <td>{{ $sale->branch->name ?? '—' }}</td>
                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td><strong>৳{{ number_format($sale->total_amount ?? 0, 2) }}</strong></td>
                            <td>৳{{ number_format($sale->discount ?? 0, 2) }}</td>
                            <td>{{ $sale->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No sales in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $sales->withQueryString()->links() }}</div>
        </div>
    </div>
</div>
@endsection
