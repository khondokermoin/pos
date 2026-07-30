@extends('layouts.admin_master')

@section('title', 'General Setup - Global Settings')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">General Setup</h2>
                <div class="mt-1 text-muted">Configure your application's basic information, timezone, and currency.</div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                {{-- Success / Error Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Settings Form --}}
                <form action="{{ route('superadmin.settings.general.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    {{-- ⚠️ অত্যন্ত গুরুত্বপূর্ণ: কন্ট্রোলারকে জানানোর জন্য যে এটি general গ্রুপ --}}
                    <input type="hidden" name="group" value="general">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Basic Application Settings</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <!-- Application Name -->
                                <div class="col-md-12">
                                    <label class="form-label required">Application Name</label>
                                    <input type="text"
                                           name="app_name"
                                           class="form-control @error('app_name') is-invalid @enderror"
                                           value="{{ old('app_name', $settings['app_name'] ?? 'Advanced Cloud POS') }}"
                                           placeholder="e.g., My SaaS Platform">
                                    @error('app_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Application Title / Slogan -->
                                <div class="col-md-12">
                                    <label class="form-label">Application Title / Slogan</label>
                                    <input type="text"
                                           name="app_title"
                                           class="form-control @error('app_title') is-invalid @enderror"
                                           value="{{ old('app_title', $settings['app_title'] ?? 'The Ultimate POS & Inventory Solution') }}"
                                           placeholder="e.g., The Ultimate POS Solution">
                                    @error('app_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Timezone -->
                                <div class="col-md-6">
                                    <label class="form-label required">Default Timezone</label>
                                    <select name="app_timezone" class="form-select @error('app_timezone') is-invalid @enderror">
                                        <option value="UTC" {{ old('app_timezone', $settings['app_timezone'] ?? 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                        <option value="Asia/Dhaka" {{ old('app_timezone', $settings['app_timezone'] ?? '') == 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka (Bangladesh)</option>
                                        <option value="Asia/Kolkata" {{ old('app_timezone', $settings['app_timezone'] ?? '') == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (India)</option>
                                        <option value="Europe/London" {{ old('app_timezone', $settings['app_timezone'] ?? '') == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                        <option value="America/New_York" {{ old('app_timezone', $settings['app_timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                    </select>
                                    @error('app_timezone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Default Language -->
                                <div class="col-md-6">
                                    <label class="form-label required">Default Language</label>
                                    <select name="app_language" class="form-select @error('app_language') is-invalid @enderror">
                                        <option value="en" {{ old('app_language', $settings['app_language'] ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                                        <option value="bn" {{ old('app_language', $settings['app_language'] ?? '') == 'bn' ? 'selected' : '' }}>Bengali (বাংলা)</option>
                                        <option value="ar" {{ old('app_language', $settings['app_language'] ?? '') == 'ar' ? 'selected' : '' }}>Arabic</option>
                                    </select>
                                    @error('app_language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Date Format -->
                                <div class="col-md-6">
                                    <label class="form-label required">Date Format</label>
                                    <select name="date_format" class="form-select @error('date_format') is-invalid @enderror">
                                        <option value="Y-m-d" {{ old('date_format', $settings['date_format'] ?? 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2026-07-14)</option>
                                        <option value="d-m-Y" {{ old('date_format', $settings['date_format'] ?? '') == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY (14-07-2026)</option>
                                        <option value="m/d/Y" {{ old('date_format', $settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (07/14/2026)</option>
                                    </select>
                                    @error('date_format')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Currency Symbol -->
                                <div class="col-md-6">
                                    <label class="form-label required">Default Currency Symbol</label>
                                    <input type="text"
                                           name="currency_symbol"
                                           class="form-control @error('currency_symbol') is-invalid @enderror"
                                           value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '৳') }}"
                                           placeholder="e.g., $, €, ৳, ₹">
                                    @error('currency_symbol')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i> Save General Settings
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
