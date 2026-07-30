@extends('layouts.admin_master')

@section('title', 'Edit Company')

@section('content')
    <!-- Page Title & Breadcrumb -->
    <div class="mb-2 row">
        <div class="col-sm-6">
            <h4 class="page-title">Edit Company</h4>
        </div>
        <div class="col-sm-6 text-sm-end">
            <nav aria-label="breadcrumb">
                <ol class="mb-0 breadcrumb justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.companies.index') }}">Companies</a></li>
                    <li class="breadcrumb-item active">{{ $company->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    @php
        // Logo URL
        $logoUrl = null;
        if (!empty($company->logo)) {
            $rawPath = trim($company->logo);
            if (\Illuminate\Support\Str::startsWith($rawPath, ['http://', 'https://'])) {
                $logoUrl = $rawPath;
            } else {
                $cleanPath = ltrim(preg_replace('/^(public|storage)\//i', '', $rawPath), '/');
                $logoUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath)
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($cleanPath)
                    : asset('storage/' . $cleanPath);
            }
        }

        // Favicon URL
        $faviconUrl = null;
        if (!empty($company->favicon)) {
            $rawFav = trim($company->favicon);
            if (\Illuminate\Support\Str::startsWith($rawFav, ['http://', 'https://'])) {
                $faviconUrl = $rawFav;
            } else {
                $cleanFav = ltrim(preg_replace('/^(public|storage)\//i', '', $rawFav), '/');
                $faviconUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists($cleanFav)
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($cleanFav)
                    : asset('storage/' . $cleanFav);
            }
        }

        // Theme settings
        $theme = is_array($company->theme_settings)
            ? $company->theme_settings
            : json_decode($company->theme_settings, true) ?? [];
        $socialLinks = is_array($company->social_links)
            ? $company->social_links
            : json_decode($company->social_links, true) ?? [];
        $contactInfo = is_array($company->contact_info)
            ? $company->contact_info
            : json_decode($company->contact_info, true) ?? [];

        // Safe access to JSON settings
        $settings = is_array($company->settings) ? $company->settings : json_decode($company->settings, true);
        $s = function ($key, $default = null) use ($settings) {
            return $settings[$key] ?? $default;
        };
    @endphp

    <form id="company-form" action="{{ route('superadmin.companies.update', $company->id) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-12">

                {{-- ==========================================
                    1. Basic Information
                ========================================== --}}
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3 header-title">Basic Information</h4>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Company Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $company->name) }}"
                                    placeholder="Enter company name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ✅ ADDED: Business Type (Missing in original edit form) --}}
                            <div class="mb-3 col-md-6">
                                <label for="business_type_id" class="form-label">Business Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('business_type_id') is-invalid @enderror"
                                    id="business_type_id" name="business_type_id" required>
                                    <option value="">Select Business Type</option>
                                    @foreach ($business_types ?? [] as $type)
                                        <option value="{{ $type->id }}" data-slug="{{ $type->slug }}"
                                            {{ old('business_type_id', $company->business_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('business_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="slug" class="form-label">Slug / URL Identifier</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                    id="slug" name="slug" value="{{ old('slug', $company->slug) }}"
                                    placeholder="auto-generated-from-name">
                                <small class="text-muted">Leave empty to auto-generate from company name.</small>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">Company Email <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $company->email) }}"
                                    placeholder="Enter company email" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="contact_person" class="form-label">Contact Person Name</label>
                                <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                                    id="contact_person" name="contact_person"
                                    value="{{ old('contact_person', $company->contact_person) }}"
                                    placeholder="Enter contact person name">
                                @error('contact_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone', $company->phone) }}"
                                    placeholder="Enter phone number">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror"
                                    id="website" name="website" value="{{ old('website', $company->website) }}"
                                    placeholder="https://example.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="user_id" class="form-label">Assign Company Admin <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('user_id') is-invalid @enderror" id="user_id"
                                    name="user_id" required>
                                    <option value="">Select Admin User</option>
                                    @foreach ($users ?? [] as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id', $company->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="border rounded p-3 bg-light">
                                    <label for="admin_password" class="form-label">Admin Password</label>
                                    <input type="password" class="form-control @error('admin_password') is-invalid @enderror"
                                        id="admin_password" name="admin_password" placeholder="Leave blank to keep current password">
                                    <small class="text-muted">Only enter a new password if you want to change the assigned admin's password.</small>
                                    @error('admin_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==========================================
                    2. Domain Configuration
                ========================================== --}}
                <div class="mt-3 card">
                    <div class="card-body">
                        <h4 class="mb-3 header-title">Domain Configuration</h4>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="subdomain" class="form-label">Subdomain</label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('subdomain') is-invalid @enderror"
                                        id="subdomain" name="subdomain"
                                        value="{{ old('subdomain', $company->subdomain) }}" placeholder="company-name">
                                    <span class="input-group-text">.yourdomain.com</span>
                                </div>
                                @error('subdomain')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="custom_domain" class="form-label">Custom Domain (White-label)</label>
                                <input type="text" class="form-control @error('custom_domain') is-invalid @enderror"
                                    id="custom_domain" name="custom_domain"
                                    value="{{ old('custom_domain', $company->custom_domain) }}"
                                    placeholder="pos.company.com">
                                @error('custom_domain')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==========================================
                    3. SaaS & POS Settings
                ========================================== --}}
                <div class="mt-3 card">
                    <div class="card-body">
                        <h4 class="mb-3 header-title">SaaS & POS Settings</h4>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="plan_id" class="form-label">Subscription Plan <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('plan_id') is-invalid @enderror" id="plan_id"
                                    name="plan_id" required>
                                    <option value="">Select Plan</option>
                                    @forelse($plans ?? [] as $plan)
                                        <option value="{{ $plan->id }}" data-price="{{ $plan->price }}"
                                            data-trial="{{ $plan->trial_days }}" data-users="{{ $plan->user_limit }}"
                                            data-branches="{{ $plan->branch_limit }}"
                                            {{ old('plan_id', $company->plan_id) == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }} - ${{ number_format($plan->price, 2) }}/month
                                        </option>
                                    @empty
                                        <option value="" disabled>No plans available. Please create plans first.
                                        </option>
                                    @endforelse
                                </select>
                                @error('plan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div id="plan-details" class="p-2 mt-2 rounded bg-light small" style="display: none;">
                                    <strong>Plan Details:</strong>
                                    <div id="plan-info"></div>
                                </div>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="status" class="form-label">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status" required>
                                    <option value="trial"
                                        {{ old('status', $company->status) == 'trial' ? 'selected' : '' }}>Trial</option>
                                    <option value="active"
                                        {{ old('status', $company->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive"
                                        {{ old('status', $company->status) == 'inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                    <option value="suspended"
                                        {{ old('status', $company->status) == 'suspended' ? 'selected' : '' }}>Suspended
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ✅ FIX 2: Added Trial Ends At field to manually fix "Expired" status --}}
                            <div class="mb-3 col-md-6">
                                <label for="trial_ends_at" class="form-label">Trial Ends At</label>
                                <input type="datetime-local"
                                    class="form-control @error('trial_ends_at') is-invalid @enderror" id="trial_ends_at"
                                    name="trial_ends_at"
                                    value="{{ old('trial_ends_at', $company->trial_ends_at ? \Carbon\Carbon::parse($company->trial_ends_at)->format('Y-m-d\TH:i') : '') }}">
                                <small class="text-muted">Set or extend the trial expiration date to fix "Expired"
                                    status.</small>
                                @error('trial_ends_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="currency" class="form-label">Default Currency</label>
                                <select class="form-select @error('currency') is-invalid @enderror" id="currency"
                                    name="currency">
                                    <option value="BDT"
                                        {{ old('currency', $company->currency) == 'BDT' ? 'selected' : '' }}>BDT -
                                        Bangladeshi Taka</option>
                                    <option value="USD"
                                        {{ old('currency', $company->currency) == 'USD' ? 'selected' : '' }}>USD - US
                                        Dollar</option>
                                    <option value="INR"
                                        {{ old('currency', $company->currency) == 'INR' ? 'selected' : '' }}>INR - Indian
                                        Rupee</option>
                                    <option value="EUR"
                                        {{ old('currency', $company->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro
                                    </option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="timezone" class="form-label">Timezone</label>
                                <select class="form-select @error('timezone') is-invalid @enderror" id="timezone"
                                    name="timezone">
                                    <option value="Asia/Dhaka"
                                        {{ old('timezone', $company->timezone) == 'Asia/Dhaka' ? 'selected' : '' }}>
                                        Asia/Dhaka (GMT+6)</option>
                                    <option value="Asia/Kolkata"
                                        {{ old('timezone', $company->timezone) == 'Asia/Kolkata' ? 'selected' : '' }}>
                                        Asia/Kolkata (GMT+5:30)</option>
                                    <option value="UTC"
                                        {{ old('timezone', $company->timezone) == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York"
                                        {{ old('timezone', $company->timezone) == 'America/New_York' ? 'selected' : '' }}>
                                        America/New_York (EST)</option>
                                </select>
                                @error('timezone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==========================================
                    3. Address Details
                ========================================== --}}
                <div class="mt-3 card">
                    <div class="card-body">
                        <h4 class="mb-3 header-title">Address Details</h4>
                        <div class="row">
                            <div class="mb-3 col-12">
                                <label for="address" class="form-label">Full Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3"
                                    placeholder="Enter full address">{{ old('address', $company->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                    id="city" name="city" value="{{ old('city', $company->city) }}"
                                    placeholder="Enter city">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                    id="country" name="country" value="{{ old('country', $company->country) }}"
                                    placeholder="Enter country">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="zip_code" class="form-label">Zip / Postal Code</label>
                                <input type="text" class="form-control @error('zip_code') is-invalid @enderror"
                                    id="zip_code" name="zip_code" value="{{ old('zip_code', $company->zip_code) }}"
                                    placeholder="Enter zip code">
                                @error('zip_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==========================================
                    4. Industry-Specific Settings (Dynamic)
                ========================================== --}}
                <div class="mt-3 card dynamic-business-section" data-applicable-to="grocery,pharmacy,food"
                    style="display: none;">
                    <div class="card-body">
                        <h4 class="mb-3 header-title text-primary"><i class="ti ti-clock me-2"></i>Expiry & Batch Tracking
                        </h4>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Track Expiry Dates?</label>
                                <select class="form-select" name="settings[track_expiry]">
                                    <option value="1"
                                        {{ $s('track_expiry') == '1' || $s('track_expiry') === true ? 'selected' : '' }}>
                                        Yes, Mandatory</option>
                                    <option value="0"
                                        {{ $s('track_expiry') == '0' || $s('track_expiry') === false ? 'selected' : '' }}>
                                        No, Not required</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Track Batch/Lot Numbers?</label>
                                <select class="form-select" name="settings[track_batch]">
                                    <option value="1"
                                        {{ $s('track_batch') == '1' || $s('track_batch') === true ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="0"
                                        {{ $s('track_batch') == '0' || $s('track_batch') === false ? 'selected' : '' }}>No
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 card dynamic-business-section" data-applicable-to="clothing,fashion,footwear"
                    style="display: none;">
                    <div class="card-body">
                        <h4 class="mb-3 header-title text-primary"><i class="ti ti-color-swatch me-2"></i>Variant &
                            Attribute Settings</h4>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Default Variant Attributes to Enable</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="settings[enable_size]"
                                        value="1" {{ $s('enable_size') ? 'checked' : '' }}>
                                    <label class="form-check-label">Size (S, M, L, XL)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="settings[enable_color]"
                                        value="1" {{ $s('enable_color') ? 'checked' : '' }}>
                                    <label class="form-check-label">Color</label>
                                </div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Default Unit for Items</label>
                                <select class="form-select" name="settings[default_unit]">
                                    <option value="pieces" {{ $s('default_unit') == 'pieces' ? 'selected' : '' }}>Pieces
                                        (pcs)</option>
                                    <option value="pairs" {{ $s('default_unit') == 'pairs' ? 'selected' : '' }}>Pairs
                                        (for footwear)</option>
                                    <option value="meters" {{ $s('default_unit') == 'meters' ? 'selected' : '' }}>Meters
                                        (for fabrics)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 card dynamic-business-section" data-applicable-to="electronics,gadgets"
                    style="display: none;">
                    <div class="card-body">
                        <h4 class="mb-3 header-title text-primary"><i class="ti ti-shield-check me-2"></i>Warranty &
                            Serial Tracking</h4>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Track IMEI / Serial Numbers?</label>
                                <select class="form-select" name="settings[track_imei]">
                                    <option value="1"
                                        {{ $s('track_imei') == '1' || $s('track_imei') === true ? 'selected' : '' }}>Yes,
                                        Mandatory</option>
                                    <option value="0"
                                        {{ $s('track_imei') == '0' || $s('track_imei') === false ? 'selected' : '' }}>No
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Default Warranty Period (Months)</label>
                                <input type="number" class="form-control" name="settings[default_warranty_months]"
                                    value="{{ $s('default_warranty_months', 12) }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==========================================
                    5. Media & Logo
                ========================================== --}}
                <div class="mt-3 card">
                    <div class="card-body">
                        <h4 class="mb-3 header-title">Company Logo</h4>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="logo" class="form-label">Upload New Logo</label>
                                <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                    id="logo" name="logo"
                                    accept="image/png, image/jpeg, image/jpg, image/svg+xml">
                                <small class="text-muted">Recommended size: 200x200px (PNG, JPG, SVG). Max 2MB. Leave empty
                                    to keep current logo.</small>
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Logo Preview</label>
                                <div id="logo-preview" class="p-2 text-center border rounded"
                                    style="min-height: 100px; background: #f8f9fa;">
                                    @if ($logoUrl)
                                        <img src="{{ $logoUrl }}" alt="Company Logo"
                                            style="max-width: 150px; max-height: 150px;" class="img-fluid">
                                    @else
                                        <i class="ti ti-photo text-muted" style="font-size: 2rem;"></i>
                                        <p class="mb-0 text-muted small">No logo selected</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==========================================
                    6. Branding & Theme Settings
                ========================================== --}}
                <div class="mt-3 card">
                    <div class="card-body">
                        <h4 class="mb-1 header-title">
                            <i class="ti ti-palette me-2 text-primary"></i>Branding & Theme
                        </h4>
                        <p class="mb-3 text-muted small">These settings control how the customer's site looks on their
                            custom domain.</p>
                        <div class="row">

                            {{-- Favicon Upload --}}
                            <div class="mb-3 col-md-6">
                                <label for="favicon" class="form-label">Favicon</label>
                                <input type="file" class="form-control @error('favicon') is-invalid @enderror"
                                    id="favicon" name="favicon"
                                    accept="image/png, image/jpeg, image/jpg, image/x-icon, image/svg+xml">
                                <small class="text-muted">Browser tab icon. PNG/ICO, max 512KB. Leave empty to keep
                                    current.</small>
                                @error('favicon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Favicon Preview</label>
                                <div id="favicon-preview"
                                    class="p-2 text-center border rounded d-flex align-items-center gap-3"
                                    style="min-height: 70px; background: #f8f9fa;">
                                    @if ($faviconUrl)
                                        <img id="favicon-preview-img" src="{{ $faviconUrl }}" alt="Favicon"
                                            width="32" height="32" style="object-fit: contain;">
                                        <span class="text-muted small">Current favicon</span>
                                    @else
                                        <i class="ti ti-browser text-muted" style="font-size: 1.5rem;"></i>
                                        <span class="text-muted small">No favicon set</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Primary Color --}}
                            <div class="mb-3 col-md-4">
                                <label for="primary_color" class="form-label">
                                    Primary Brand Color
                                    <span class="badge ms-1" id="primary-color-badge"
                                        style="background-color: {{ $theme['primary_color'] ?? '#3B82F6' }};">
                                        {{ $theme['primary_color'] ?? '#3B82F6' }}
                                    </span>
                                </label>
                                <input type="color"
                                    class="form-control form-control-color @error('primary_color') is-invalid @enderror"
                                    id="primary_color" name="primary_color"
                                    value="{{ old('primary_color', $theme['primary_color'] ?? '#3B82F6') }}"
                                    title="Choose primary brand color">
                                <small class="text-muted">Main color for buttons, headers, links.</small>
                                @error('primary_color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Secondary Color --}}
                            <div class="mb-3 col-md-4">
                                <label for="secondary_color" class="form-label">
                                    Secondary Color
                                    <span class="badge ms-1" id="secondary-color-badge"
                                        style="background-color: {{ $theme['secondary_color'] ?? '#1E40AF' }};">
                                        {{ $theme['secondary_color'] ?? '#1E40AF' }}
                                    </span>
                                </label>
                                <input type="color"
                                    class="form-control form-control-color @error('secondary_color') is-invalid @enderror"
                                    id="secondary_color" name="secondary_color"
                                    value="{{ old('secondary_color', $theme['secondary_color'] ?? '#1E40AF') }}"
                                    title="Choose secondary color">
                                <small class="text-muted">Sidebar, footer background.</small>
                                @error('secondary_color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Accent Color --}}
                            <div class="mb-3 col-md-4">
                                <label for="accent_color" class="form-label">
                                    Accent Color
                                    <span class="badge ms-1" id="accent-color-badge"
                                        style="background-color: {{ $theme['accent_color'] ?? '#F59E0B' }};">
                                        {{ $theme['accent_color'] ?? '#F59E0B' }}
                                    </span>
                                </label>
                                <input type="color"
                                    class="form-control form-control-color @error('accent_color') is-invalid @enderror"
                                    id="accent_color" name="accent_color"
                                    value="{{ old('accent_color', $theme['accent_color'] ?? '#F59E0B') }}"
                                    title="Choose accent color">
                                <small class="text-muted">Highlights, badges, alerts.</small>
                                @error('accent_color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Live Theme Preview --}}
                            <div class="mb-3 col-12">
                                <label class="form-label">Live Theme Preview</label>
                                <div id="theme-preview" class="p-3 rounded border d-flex align-items-center gap-3"
                                    style="background: #f8f9fa;">
                                    <div id="preview-header" class="px-3 py-2 rounded text-white small fw-bold"
                                        style="background-color: {{ $theme['primary_color'] ?? '#3B82F6' }};">
                                        Header / Button
                                    </div>
                                    <div id="preview-sidebar" class="px-3 py-2 rounded text-white small"
                                        style="background-color: {{ $theme['secondary_color'] ?? '#1E40AF' }};">
                                        Sidebar
                                    </div>
                                    <div id="preview-accent" class="px-3 py-2 rounded text-white small"
                                        style="background-color: {{ $theme['accent_color'] ?? '#F59E0B' }};">
                                        Badge / Alert
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==========================================
                    7. Social Links & Contact Info (React Footer)
                ========================================== --}}
                <div class="mt-3 card">
                    <div class="card-body">
                        <h4 class="mb-1 header-title">
                            <i class="ti ti-share me-2 text-primary"></i>Social Links & Contact Info
                        </h4>
                        <p class="mb-3 text-muted small">These appear in the React frontend footer on the customer's
                            domain.</p>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="social_facebook" class="form-label"><i
                                        class="ti ti-brand-facebook me-1 text-primary"></i>Facebook Page URL</label>
                                <input type="url" class="form-control @error('social_facebook') is-invalid @enderror"
                                    id="social_facebook" name="social_facebook"
                                    value="{{ old('social_facebook', $socialLinks['facebook'] ?? '') }}"
                                    placeholder="https://facebook.com/yourpage">
                                @error('social_facebook')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="social_instagram" class="form-label"><i
                                        class="ti ti-brand-instagram me-1 text-danger"></i>Instagram URL</label>
                                <input type="url"
                                    class="form-control @error('social_instagram') is-invalid @enderror"
                                    id="social_instagram" name="social_instagram"
                                    value="{{ old('social_instagram', $socialLinks['instagram'] ?? '') }}"
                                    placeholder="https://instagram.com/yourpage">
                                @error('social_instagram')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="social_twitter" class="form-label"><i
                                        class="ti ti-brand-twitter me-1 text-info"></i>Twitter / X URL</label>
                                <input type="url" class="form-control @error('social_twitter') is-invalid @enderror"
                                    id="social_twitter" name="social_twitter"
                                    value="{{ old('social_twitter', $socialLinks['twitter'] ?? '') }}"
                                    placeholder="https://twitter.com/yourpage">
                                @error('social_twitter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="social_youtube" class="form-label"><i
                                        class="ti ti-brand-youtube me-1 text-danger"></i>YouTube Channel URL</label>
                                <input type="url" class="form-control @error('social_youtube') is-invalid @enderror"
                                    id="social_youtube" name="social_youtube"
                                    value="{{ old('social_youtube', $socialLinks['youtube'] ?? '') }}"
                                    placeholder="https://youtube.com/yourchannel">
                                @error('social_youtube')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <hr class="my-2">
                            </div>

                            <div class="mb-3 col-md-4">
                                <label for="contact_phone" class="form-label"><i class="ti ti-phone me-1"></i>Support
                                    Phone (Footer)</label>
                                <input type="text" class="form-control @error('contact_phone') is-invalid @enderror"
                                    id="contact_phone" name="contact_phone"
                                    value="{{ old('contact_phone', $contactInfo['phone'] ?? ($company->phone ?? '')) }}"
                                    placeholder="01XXXXXXXXX">
                                @error('contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="contact_email" class="form-label"><i class="ti ti-mail me-1"></i>Support
                                    Email (Footer)</label>
                                <input type="email" class="form-control @error('contact_email') is-invalid @enderror"
                                    id="contact_email" name="contact_email"
                                    value="{{ old('contact_email', $contactInfo['email'] ?? ($company->email ?? '')) }}"
                                    placeholder="support@company.com">
                                @error('contact_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="contact_address" class="form-label"><i class="ti ti-map-pin me-1"></i>Display
                                    Address (Footer)</label>
                                <input type="text" class="form-control @error('contact_address') is-invalid @enderror"
                                    id="contact_address" name="contact_address"
                                    value="{{ old('contact_address', $contactInfo['address'] ?? ($company->address ?? '')) }}"
                                    placeholder="Dhaka, Bangladesh">
                                @error('contact_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==========================================
                    Action Buttons
                ========================================== --}}
                <div class="mt-3 card">
                    <div class="card-body text-end">
                        <a href="{{ route('superadmin.companies.index') }}" class="btn btn-secondary me-2">
                            <i class="ti ti-x me-1"></i> Cancel
                        </a>
                        <button type="submit" id="submit-btn" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Update Company
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // ==========================================
            // 0. Dynamic Business Type Field Toggling
            // ==========================================
            function toggleBusinessFields() {
                const selectedOption = $('#business_type_id option:selected');
                const businessSlug = selectedOption.data('slug');
                $('.dynamic-business-section').hide();

                if (businessSlug) {
                    $('.dynamic-business-section').each(function() {
                        const applicableTo = $(this).data('applicable-to').split(',');
                        if (applicableTo.includes(businessSlug)) {
                            $(this).fadeIn(300);
                        }
                    });
                }
            }
            // Call on load to show relevant sections for existing company
            toggleBusinessFields();
            $('#business_type_id').on('change', toggleBusinessFields);

            // ==========================================
            // 1. Auto-generate slug from company name
            // ==========================================
            if ($('#slug').val()) {
                $('#slug').data('manual', true);
            }
            $('#name').on('input', function() {
                let slugInput = $('#slug');
                if (!slugInput.data('manual')) {
                    slugInput.val($(this).val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(
                        /^-+|-+$/g, ''));
                }
            });
            $('#slug').on('input', function() {
                $(this).data('manual', $(this).val().trim() !== '');
            });

            // ==========================================
            // 2. Logo Preview with Safe Reset
            // ==========================================
            const maxLogoSizeBytes = 2 * 1024 * 1024; // 2MB
            const allowedLogoTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'];
            let currentLogoUrl = "{{ $logoUrl ?? '' }}";

            $('#logo').on('change', function(e) {
                const file = e.target.files[0];
                const preview = $('#logo-preview');
                const logoInputEl = this;

                if (!file) {
                    if (currentLogoUrl) {
                        preview.html(
                            `<img src="${currentLogoUrl}" alt="Company Logo" style="max-width: 150px; max-height: 150px;" class="img-fluid">`
                        );
                    } else {
                        preview.html(
                            `<i class="ti ti-photo text-muted" style="font-size: 2rem;"></i><p class="mb-0 text-muted small">No logo selected</p>`
                        );
                    }
                    return;
                }

                if (file.size > maxLogoSizeBytes) {
                    alert('Logo size must not exceed 2MB. Please choose a smaller file.');
                    $(logoInputEl).val('');
                    if (currentLogoUrl) {
                        preview.html(
                            `<img src="${currentLogoUrl}" alt="Company Logo" style="max-width: 150px; max-height: 150px;" class="img-fluid">`
                        );
                    }
                    return;
                }

                if (!allowedLogoTypes.includes(file.type)) {
                    alert('Only PNG, JPG or SVG images are allowed.');
                    $(logoInputEl).val('');
                    if (currentLogoUrl) {
                        preview.html(
                            `<img src="${currentLogoUrl}" alt="Company Logo" style="max-width: 150px; max-height: 150px;" class="img-fluid">`
                        );
                    }
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.html(
                        `<img src="${e.target.result}" alt="Logo Preview" style="max-width: 150px; max-height: 150px;" class="img-fluid">`
                    );
                };
                reader.readAsDataURL(file);
            });

            // ==========================================
            // 3. Favicon Preview
            // ==========================================
            const allowedFaviconTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/x-icon', 'image/svg+xml'];
            const maxFaviconBytes = 512 * 1024; // 512KB
            let currentFaviconUrl = "{{ $faviconUrl ?? '' }}";

            $('#favicon').on('change', function(e) {
                const file = e.target.files[0];
                const preview = $('#favicon-preview');

                if (!file) {
                    if (currentFaviconUrl) {
                        preview.html(
                            `<img src="${currentFaviconUrl}" alt="Favicon" width="32" height="32" style="object-fit:contain;"><span class="text-muted small ms-2">Current favicon</span>`
                            );
                    } else {
                        preview.html(
                            `<i class="ti ti-browser text-muted" style="font-size:1.5rem;"></i><span class="text-muted small ms-2">No favicon set</span>`
                            );
                    }
                    return;
                }

                if (file.size > maxFaviconBytes) {
                    alert('Favicon size must not exceed 512KB.');
                    $(this).val('');
                    return;
                }

                if (!allowedFaviconTypes.includes(file.type)) {
                    alert('Only PNG, JPG, ICO or SVG images are allowed for favicon.');
                    $(this).val('');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.html(
                        `<img src="${ev.target.result}" alt="Favicon Preview" width="32" height="32" style="object-fit:contain;"><span class="text-muted small ms-2">New favicon (preview)</span>`
                        );
                };
                reader.readAsDataURL(file);
            });

            // ==========================================
            // 4. Live Color Badge & Theme Preview Update
            // ==========================================
            function updateColorBadge(inputId, badgeId, previewId) {
                $('#' + inputId).on('input change', function() {
                    const color = $(this).val();
                    $('#' + badgeId).css('background-color', color).text(color);
                    $('#' + previewId).css('background-color', color);
                });
            }

            updateColorBadge('primary_color', 'primary-color-badge', 'preview-header');
            updateColorBadge('secondary_color', 'secondary-color-badge', 'preview-sidebar');
            updateColorBadge('accent_color', 'accent-color-badge', 'preview-accent');

            // ==========================================
            // 5. Plan Details Display
            // ==========================================
            function updatePlanDetails() {
                const selectedOption = $('#plan_id option:selected');
                const detailsDiv = $('#plan-details');
                const infoDiv = $('#plan-info');

                if ($('#plan_id').val() && selectedOption.data('price') !== undefined) {
                    const price = selectedOption.data('price');
                    const trial = selectedOption.data('trial');
                    const users = selectedOption.data('users');
                    const branches = selectedOption.data('branches');

                    infoDiv.html(`
                        <div class="mt-1">
                            <strong>Price:</strong> $${parseFloat(price).toFixed(2)}/month<br>
                            <strong>Trial Period:</strong> ${trial} days<br>
                            <strong>User Limit:</strong> ${users} users<br>
                            <strong>Branch Limit:</strong> ${branches} branches
                        </div>
                    `);
                    detailsDiv.show();
                } else {
                    detailsDiv.hide();
                }
            }

            $('#plan_id').on('change', updatePlanDetails);
            updatePlanDetails(); // Trigger on load

            // ==========================================
            // 4. Live Validation (Blur Event)
            // ==========================================
            $('#company-form').on('blur', '.form-control, .form-select', function() {
                let input = $(this);
                if (this.type === 'file') return;

                if (!this.checkValidity()) {
                    input.addClass('is-invalid');
                    let feedback = input.siblings('.invalid-feedback').first();
                    if (!feedback.length && input.parent().hasClass('input-group')) {
                        feedback = input.parent().next('.invalid-feedback');
                    }

                    if (feedback.length) {
                        feedback.text(this.validationMessage || 'Invalid input.').show().css('display',
                            'block');
                    } else {
                        let target = input.parent().hasClass('input-group') ? input.parent() : input;
                        target.after('<div class="invalid-feedback" style="display:block;">' + (this
                            .validationMessage || 'Invalid input.') + '</div>');
                    }
                }
            });

            // ==========================================
            // 5. Live Error Clearing (Input Event)
            // ==========================================
            $('#company-form').on('input change', '.form-control, .form-select', function() {
                let input = $(this);
                input.removeClass('is-invalid');

                let feedback = input.siblings('.invalid-feedback').first();
                if (!feedback.length && input.parent().hasClass('input-group')) {
                    feedback = input.parent().next('.invalid-feedback');
                }

                if (feedback.length) {
                    feedback.text('').hide();
                } else {
                    let target = input.parent().hasClass('input-group') ? input.parent() : input;
                    target.next('.invalid-feedback').remove();
                }
            });

            // ==========================================
            // 6. AJAX Form Submission
            // ==========================================
            $('#company-form').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = new FormData(this);
                let url = form.attr('action');
                let submitBtn = $('#submit-btn');
                let originalBtnHtml = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Updating...');

                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').text('').hide();
                form.find('.invalid-feedback[style*="display: block"]').remove();

                $.ajax({
                    url: url,
                    type: 'POST', // Laravel handles PUT via @method('PUT') hidden field
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        toastr.success(response.message || 'Company updated successfully!',
                            'Success');
                        const redirectUrl = response.redirect ||
                            '{{ route('superadmin.companies.index') }}';
                        setTimeout(function() {
                            window.location.href = redirectUrl;
                        }, 1200);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let firstErrorElement = null;

                            $.each(errors, function(key, messages) {
                                let input = form.find('[name="' + key + '"], [name="' +
                                    key + '[]"]');
                                if (input.length) {
                                    input.addClass('is-invalid');
                                    let feedback = input.siblings('.invalid-feedback')
                                        .first();
                                    if (!feedback.length && input.parent().hasClass(
                                            'input-group')) {
                                        feedback = input.parent().next(
                                            '.invalid-feedback');
                                    }

                                    if (feedback.length) {
                                        feedback.text(messages[0]).show().css('display',
                                            'block');
                                    } else {
                                        let target = input.parent().hasClass(
                                            'input-group') ? input.parent() : input;
                                        target.after(
                                            '<div class="invalid-feedback" style="display:block;">' +
                                            messages[0] + '</div>');
                                    }

                                    if (!firstErrorElement) firstErrorElement = input;
                                }
                            });

                            if (firstErrorElement) {
                                $('html, body').animate({
                                    scrollTop: firstErrorElement.offset().top - 150
                                }, 500);
                            }
                        } else {
                            toastr.error('An unexpected error occurred. Please try again.',
                                'Error');
                            console.error(xhr.responseText);
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalBtnHtml);
                    }
                });
            });
        });
    </script>
@endpush
