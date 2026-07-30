@extends('layouts.admin_master')

@section('title', 'Invoice Settings')

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-file-invoice me-2 text-primary"></i>Invoice Settings</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Invoice Settings</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Left: Settings Form --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-settings me-2"></i>Invoice Configuration</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('company.settings.invoice.update') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Invoice Prefix</label>
                                <input type="text" name="invoice_prefix"
                                    class="form-control @error('invoice_prefix') is-invalid @enderror"
                                    value="{{ old('invoice_prefix', $company->invoice_prefix ?? 'INV-') }}"
                                    placeholder="e.g., INV-, ORD-, SALE-" maxlength="10">
                                @error('invoice_prefix')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Invoice numbers will be: <strong
                                        id="prefixPreview">{{ $company->invoice_prefix ?? 'INV-' }}</strong>0001</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Currency Symbol</label>
                                <input type="text" name="currency_symbol"
                                    class="form-control @error('currency_symbol') is-invalid @enderror"
                                    value="{{ old('currency_symbol', $company->currency_symbol ?? '৳') }}"
                                    placeholder="e.g., ৳, $, €, £" maxlength="5">
                                @error('currency_symbol')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Displayed before amounts on invoices.</small>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Show Logo on Invoice</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="show_logo" id="showLogoToggle"
                                        value="1" {{ old('show_logo', $company->show_logo ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="showLogoToggle">
                                        Display company logo on printed invoices
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Invoice Footer Text</label>
                                <textarea name="invoice_footer" class="form-control @error('invoice_footer') is-invalid @enderror" rows="3"
                                    placeholder="e.g., Thank you for your business! Returns accepted within 7 days.">{{ old('invoice_footer', $company->invoice_footer) }}</textarea>
                                @error('invoice_footer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">This text appears at the bottom of every invoice.</small>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ti ti-device-floppy me-1"></i> Save Invoice Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Live Preview --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-eye me-2"></i>Invoice Preview</h5>
                </div>
                <div class="card-body">
                    <div class="border rounded p-3 bg-white" style="font-family: monospace; font-size: 0.85rem;">
                        {{-- Mini Invoice Preview --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                @if ($company->logo && ($company->show_logo ?? true))
                                    <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo"
                                        style="height:40px;">
                                @else
                                    <div class="fw-bold fs-5">{{ $company->name }}</div>
                                @endif
                                <small class="text-muted d-block">{{ $company->address ?? 'Company Address' }}</small>
                                <small class="text-muted d-block">{{ $company->phone ?? 'Phone' }}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">INVOICE</div>
                                <div class="text-muted small">
                                    #<span id="previewPrefix">{{ $company->invoice_prefix ?? 'INV-' }}</span>0001
                                </div>
                                <div class="text-muted small">{{ date('d M Y') }}</div>
                            </div>
                        </div>

                        <hr>

                        <table class="table table-sm table-bordered mb-2" style="font-size:0.75rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Sample Product × 2</td>
                                    <td class="text-end">
                                        <span id="previewCurrency">{{ $company->currency_symbol ?? '৳' }}</span>200.00
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-end fw-bold">Total</td>
                                    <td class="text-end fw-bold">
                                        <span id="previewCurrencyTotal">{{ $company->currency_symbol ?? '৳' }}</span>200.00
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="text-center text-muted small mt-2 border-top pt-2" id="previewFooter">
                            {{ $company->invoice_footer ?? 'Thank you for your business!' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Live preview updates
        document.querySelector('[name="invoice_prefix"]').addEventListener('input', function() {
            const val = this.value || 'INV-';
            document.getElementById('prefixPreview').textContent = val;
            document.getElementById('previewPrefix').textContent = val;
        });

        document.querySelector('[name="currency_symbol"]').addEventListener('input', function() {
            const val = this.value || '৳';
            document.getElementById('previewCurrency').textContent = val;
            document.getElementById('previewCurrencyTotal').textContent = val;
        });

        document.querySelector('[name="invoice_footer"]').addEventListener('input', function() {
            document.getElementById('previewFooter').textContent = this.value || 'Thank you for your business!';
        });
    </script>
@endpush
