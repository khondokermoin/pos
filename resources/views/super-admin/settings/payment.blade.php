@extends('layouts.admin_master')

@section('title', 'Payment Gateways - Global Settings')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Payment Gateways</h2>
                <div class="mt-1 text-muted">Configure your SaaS platform's payment methods and API credentials.</div>
            </div>
            <div class="col-auto">
                <div class="btn-list">
                    <a href="{{ url()->previous() }}" class="btn">
                        <i class="ti ti-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <form action="{{ route('superadmin.settings.payment.update') }}" method="POST" id="paymentSettingsForm">
                    @csrf
                    <input type="hidden" name="group" value="payment">

                    <!-- ========================================== -->
                    <!-- Currency Settings -->
                    <!-- ========================================== -->
                    <div class="mb-4 card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ti ti-currency-taka me-2"></i> Currency Settings</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Default Currency</label>
                                    <select name="default_currency" class="form-select @error('default_currency') is-invalid @enderror">
                                        <option value="BDT" {{ old('default_currency', $settings['default_currency'] ?? 'BDT') == 'BDT' ? 'selected' : '' }}>BDT - Bangladeshi Taka</option>
                                        <option value="USD" {{ old('default_currency', $settings['default_currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                        <option value="EUR" {{ old('default_currency', $settings['default_currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    </select>
                                    @error('default_currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Currency Symbol Position</label>
                                    <select name="currency_symbol_position" class="form-select">
                                        <option value="before" {{ old('currency_symbol_position', $settings['currency_symbol_position'] ?? 'before') == 'before' ? 'selected' : '' }}>Before (৳100)</option>
                                        <option value="after" {{ old('currency_symbol_position', $settings['currency_symbol_position'] ?? '') == 'after' ? 'selected' : '' }}>After (100৳)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- SSLCommerz Configuration -->
                    <!-- ========================================== -->
                    <div class="mb-4 card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-shield-check me-2 text-success"></i> SSLCommerz Configuration
                                <span class="badge bg-success-lt text-success ms-2">Most Popular in BD</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-check form-switch">
                                        <input type="hidden" name="sslcommerz_enabled" value="0">
                                        <input class="form-check-input" type="checkbox" name="sslcommerz_enabled" value="1"
                                            {{ old('sslcommerz_enabled', $settings['sslcommerz_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                        <span class="form-check-label">Enable SSLCommerz Payment Gateway</span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Store ID</label>
                                    <input type="text" name="sslcommerz_store_id" class="form-control @error('sslcommerz_store_id') is-invalid @enderror"
                                        value="{{ old('sslcommerz_store_id', $settings['sslcommerz_store_id'] ?? '') }}" placeholder="your_store_id">
                                    @error('sslcommerz_store_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Store Password</label>
                                    <!-- SECURITY: No $settings fallback. Controller will skip update if empty. -->
                                    <input type="password" name="sslcommerz_store_password" class="form-control @error('sslcommerz_store_password') is-invalid @enderror"
                                        value="{{ old('sslcommerz_store_password') }}" placeholder="Leave blank to keep current">
                                    @error('sslcommerz_store_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Environment</label>
                                    <select name="sslcommerz_environment" class="form-select">
                                        <option value="sandbox" {{ old('sslcommerz_environment', $settings['sslcommerz_environment'] ?? 'sandbox') == 'sandbox' ? 'selected' : '' }}>Sandbox (Test)</option>
                                        <option value="live" {{ old('sslcommerz_environment', $settings['sslcommerz_environment'] ?? '') == 'live' ? 'selected' : '' }}>Live (Production)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- bKash Configuration -->
                    <!-- ========================================== -->
                    <div class="mb-4 card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ti ti-wallet me-2 text-pink"></i> bKash Configuration</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-check form-switch">
                                        <input type="hidden" name="bkash_enabled" value="0">
                                        <input class="form-check-input" type="checkbox" name="bkash_enabled" value="1"
                                            {{ old('bkash_enabled', $settings['bkash_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                        <span class="form-check-label">Enable bKash Payment (Merchant/API)</span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">App Key</label>
                                    <input type="text" name="bkash_app_key" class="form-control @error('bkash_app_key') is-invalid @enderror"
                                        value="{{ old('bkash_app_key', $settings['bkash_app_key'] ?? '') }}" placeholder="bkash_app_key">
                                    @error('bkash_app_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">App Secret</label>
                                    <!-- SECURITY: No $settings fallback -->
                                    <input type="password" name="bkash_app_secret" class="form-control @error('bkash_app_secret') is-invalid @enderror"
                                        value="{{ old('bkash_app_secret') }}" placeholder="Leave blank to keep current">
                                    @error('bkash_app_secret') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="bkash_username" class="form-control @error('bkash_username') is-invalid @enderror"
                                        value="{{ old('bkash_username', $settings['bkash_username'] ?? '') }}" placeholder="username">
                                    @error('bkash_username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <!-- SECURITY: No $settings fallback -->
                                    <input type="password" name="bkash_password" class="form-control @error('bkash_password') is-invalid @enderror"
                                        value="{{ old('bkash_password') }}" placeholder="Leave blank to keep current">
                                    @error('bkash_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- Nagad Configuration -->
                    <!-- ========================================== -->
                    <div class="mb-4 card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ti ti-phone me-2 text-orange"></i> Nagad Configuration</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-check form-switch">
                                        <input type="hidden" name="nagad_enabled" value="0">
                                        <input class="form-check-input" type="checkbox" name="nagad_enabled" value="1"
                                            {{ old('nagad_enabled', $settings['nagad_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                        <span class="form-check-label">Enable Nagad Payment</span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Merchant ID</label>
                                    <input type="text" name="nagad_merchant_id" class="form-control @error('nagad_merchant_id') is-invalid @enderror"
                                        value="{{ old('nagad_merchant_id', $settings['nagad_merchant_id'] ?? '') }}" placeholder="merchant_id">
                                    @error('nagad_merchant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Merchant Number</label>
                                    <input type="text" name="nagad_merchant_number" class="form-control @error('nagad_merchant_number') is-invalid @enderror"
                                        value="{{ old('nagad_merchant_number', $settings['nagad_merchant_number'] ?? '') }}" placeholder="01XXXXXXXXX">
                                    @error('nagad_merchant_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- Stripe Configuration -->
                    <!-- ========================================== -->
                    <div class="mb-4 card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ti ti-credit-card me-2"></i> Stripe Configuration</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-check form-switch">
                                        <input type="hidden" name="stripe_enabled" value="0">
                                        <input class="form-check-input" type="checkbox" name="stripe_enabled" value="1"
                                            {{ old('stripe_enabled', $settings['stripe_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                        <span class="form-check-label">Enable Stripe Payment Gateway</span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Publishable Key</label>
                                    <input type="text" name="stripe_publishable_key" class="form-control @error('stripe_publishable_key') is-invalid @enderror"
                                        value="{{ old('stripe_publishable_key', $settings['stripe_publishable_key'] ?? '') }}" placeholder="pk_test_...">
                                    @error('stripe_publishable_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Secret Key</label>
                                    <!-- SECURITY: No $settings fallback -->
                                    <input type="password" name="stripe_secret_key" class="form-control @error('stripe_secret_key') is-invalid @enderror"
                                        value="{{ old('stripe_secret_key') }}" placeholder="Leave blank to keep current">
                                    @error('stripe_secret_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- PayPal Configuration -->
                    <!-- ========================================== -->
                    <div class="mb-4 card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ti ti-brand-paypal me-2"></i> PayPal Configuration</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-check form-switch">
                                        <input type="hidden" name="paypal_enabled" value="0">
                                        <input class="form-check-input" type="checkbox" name="paypal_enabled" value="1"
                                            {{ old('paypal_enabled', $settings['paypal_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                        <span class="form-check-label">Enable PayPal Payment Gateway</span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Client ID</label>
                                    <input type="text" name="paypal_client_id" class="form-control @error('paypal_client_id') is-invalid @enderror"
                                        value="{{ old('paypal_client_id', $settings['paypal_client_id'] ?? '') }}">
                                    @error('paypal_client_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Secret</label>
                                    <!-- SECURITY: No $settings fallback -->
                                    <input type="password" name="paypal_secret" class="form-control @error('paypal_secret') is-invalid @enderror"
                                        value="{{ old('paypal_secret') }}" placeholder="Leave blank to keep current">
                                    @error('paypal_secret') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- Submit Button -->
                    <!-- ========================================== -->
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Save Payment Settings
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==========================================
    // 1. TOASTER NOTIFICATION (Safe Injection)
    // ==========================================
    @if(session('success'))
        if (typeof toastr !== 'undefined') {
            toastr.success(@json(session('success')), "Success");
        } else {
            alert("Success: {{ session('success') }}");
        }
    @endif

    @if(session('error'))
        if (typeof toastr !== 'undefined') {
            toastr.error(@json(session('error')), "Error");
        } else {
            alert("Error: {{ session('error') }}");
        }
    @endif

    // ==========================================
    // 2. FORM VALIDATION (Fixed Checkbox Targeting)
    // ==========================================
    const form = document.getElementById('paymentSettingsForm');
    
    form.addEventListener('submit', function(e) {
        const enabledGateways = [];
        
        // FIX: Specifically target input[type="checkbox"] to avoid matching the hidden input (value="0")
        if (document.querySelector('input[type="checkbox"][name="sslcommerz_enabled"]')?.checked) enabledGateways.push('SSLCommerz');
        if (document.querySelector('input[type="checkbox"][name="bkash_enabled"]')?.checked) enabledGateways.push('bKash');
        if (document.querySelector('input[type="checkbox"][name="nagad_enabled"]')?.checked) enabledGateways.push('Nagad');
        if (document.querySelector('input[type="checkbox"][name="stripe_enabled"]')?.checked) enabledGateways.push('Stripe');
        if (document.querySelector('input[type="checkbox"][name="paypal_enabled"]')?.checked) enabledGateways.push('PayPal');

        if (enabledGateways.length === 0) {
            e.preventDefault(); // Stop form submission
            
            if (typeof toastr !== 'undefined') {
                toastr.warning('Please enable at least one payment gateway', 'Validation');
            } else {
                alert('Validation: Please enable at least one payment gateway');
            }
        }
    });
});
</script>
@endpush

@endsection