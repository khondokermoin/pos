@extends('layouts.admin_master')
@section('title', 'Barcode Printing')
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-barcode me-2 text-primary"></i>Barcode Printing</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('branch.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Barcode Printing</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            {{-- Search --}}
            <div class="card mb-3">
                <div class="card-body py-2">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-8">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Search product name..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                <i class="ti ti-search me-1"></i>Search
                            </button>
                            <a href="{{ route('branch.inventory.barcode') }}"
                                class="btn btn-outline-secondary btn-sm flex-fill">
                                <i class="ti ti-x"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <form method="POST" action="{{ route('branch.inventory.barcode.print') }}" id="barcodeForm">
                @csrf
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Select Products to Print</h6>
                        <div class="d-flex gap-2 align-items-center">
                            <label class="form-label mb-0 small">Copies per label:</label>
                            <input type="number" name="copies" class="form-control form-control-sm" style="width:70px;"
                                value="1" min="1" max="100">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti ti-printer me-1"></i>Print Selected
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3" style="width:40px;">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Product</th>
                                        <th class="text-center">Stock</th>
                                        <th>Barcode / SKU</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stocks as $stock)
                                        <tr>
                                            <td class="ps-3">
                                                <input type="checkbox" name="variant_ids[]"
                                                    value="{{ $stock->variant->id }}" class="form-check-input item-check">
                                            </td>
                                            <td>
                                                <strong>{{ optional($stock->variant->product)->name ?? '—' }}</strong>
                                                @if ($stock->variant->name ?? false)
                                                    <small class="text-muted d-block">{{ $stock->variant->name }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-primary-subtle text-primary">{{ $stock->quantity }}</span>
                                            </td>
                                            <td>
                                                <code
                                                    class="small">{{ $stock->variant->sku ?? ($stock->variant->barcode ?? 'N/A') }}</code>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="ti ti-barcode-off d-block mb-3"
                                                    style="font-size:3rem;opacity:.4;"></i>
                                                <h5>No Products Found</h5>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($stocks->hasPages())
                            <div class="px-3 py-2 border-top">
                                {{ $stocks->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="ti ti-info-circle me-2"></i>How to Print</h6>
                </div>
                <div class="card-body">
                    <ol class="small text-muted ps-3">
                        <li class="mb-2">Select the products you want to print barcodes for.</li>
                        <li class="mb-2">Set the number of copies per label.</li>
                        <li class="mb-2">Click <strong>Print Selected</strong>.</li>
                        <li class="mb-2">A print-ready page will open — use your browser's print function.</li>
                    </ol>
                    <div class="alert alert-info small mb-0">
                        <i class="ti ti-bulb me-1"></i>
                        Tip: Use a barcode label printer for best results. Standard A4 paper also works.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            document.querySelectorAll('.item-check').forEach(cb => cb.checked = this.checked);
        });
    </script>
@endpush
