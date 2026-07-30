@extends('layouts.admin_master')

@section('content')
    <div class="container-fluid">
        <div class="page-title-box d-flex justify-content-between align-items-center">
            <h4 class="page-title">All Sales</h4>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Total Revenue</h6>
                            <h2>৳{{ number_format($totalRevenue, 2) }}</h2>
                        </div>
                        <i class="ti ti-currency-taka fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Today's Revenue</h6>
                            <h2>৳{{ number_format($todayRevenue, 2) }}</h2>
                        </div>
                        <i class="ti ti-calendar-stats fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search invoice #..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter me-1"></i>
                            Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Branch</th>
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
                                    <td>{{ $sale->branch->name ?? '—' }}</td>
                                    <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                    <td>{{ $sale->items_count ?? 0 }}</td>
                                    <td><strong>৳{{ number_format($sale->total_amount ?? 0, 2) }}</strong></td>
                                    <td>{{ $sale->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No sales found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">Showing {{ $sales->firstItem() ?? 0 }} to {{ $sales->lastItem() ?? 0 }}
                        of {{ $sales->total() }}</div>
                    {{ $sales->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
