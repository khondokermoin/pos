@extends('layouts.admin_master')

@section('title', 'Add Company')

@section('content')
    <div class="mb-2 row">
        <div class="col-sm-6">
            <h4 class="page-title">Add New Company</h4>
        </div>
        <div class="col-sm-6 text-sm-end">
            <nav aria-label="breadcrumb">
                <ol class="mb-0 breadcrumb justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.companies.index') }}">Companies</a></li>
                    <li class="breadcrumb-item active">Add Company</li>
                </ol>
            </nav>
        </div>
    </div>

    <form id="company-form" action="{{ route('superadmin.companies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

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
                                    id="name" name="name" value="{{ old('name') }}"
                                    placeholder="Enter company name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ✅ V1 Feature: Business Type (Dynamic Fields Trigger) --}}
                            <div class="mb-3 col-md-6">
                                <label for="business_type_id" class="form-label">Business Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('business_type_id') is-invalid @enderror"
                                    id="business_type_id" name="business_type_id" required>
                                    <option value="">Select Business Type</option>
                                    @foreach ($business_types as $type)
                                        <option value="{{ $type->id }}" data-slug="{{ $type->slug }}"
                                            {{ old('business_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted"></small>
                                @error('business_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="slug" class="form-label">Slug / URL Identifier</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                    id="slug" name="slug" value="{{ old('slug') }}"
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
                                    id="email" name="email" value="{{ old('email') }}"
                                    placeholder="Enter company email" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="contact_person" class="form-label">Contact Person Name</label>
                                <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                                    id="contact_person" name="contact_person" value="{{ old('contact_person') }}"
                                    placeholder="Enter contact person name">
                                @error('contact_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone') }}"
                                    placeholder="Enter phone number">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror"
                                    id="website" name="website" value="{{ old('website') }}"
                                    placeholder="https://example.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="user_id" class="form-label">Assign Company Admin <span
                                        class="text-danger">*</span></label>
                                {{-- Allow creating new admin inline when needed --}}
                                <div class="input-group mb-2">
                                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id"
                                        name="user_id">
                                        <option value="">Select Existing Admin (optional)</option>
                                        @foreach ($users ?? [] as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button" id="toggle-new-admin">New
                                        Admin</button>
                                </div>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div id="new-admin-section" style="display: none;">
                                    <div class="mb-3">
                                        <label for="admin_name" class="form-label">Admin Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="admin_name" id="admin_name"
                                            class="form-control @error('admin_name') is-invalid @enderror"
                                            value="{{ old('admin_name') }}">
                                        @error('admin_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="admin_email" class="form-label">Admin Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" name="admin_email" id="admin_email"
                                            class="form-control @error('admin_email') is-invalid @enderror"
                                            value="{{ old('admin_email') }}">
                                        @error('admin_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="admin_password" class="form-label">Password <span
                                                class="text-danger">*</span></label>
                                        <input type="password" name="admin_password" id="admin_password"
                                            class="form-control @error('admin_password') is-invalid @enderror">
                                        @error('admin_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="admin_password_confirmation" class="form-label">Confirm Password <span
                                                class="text-danger">*</span></label>
                                        <input type="password" name="admin_password_confirmation"
                                            id="admin_password_confirmation" class="form-control">
                                    </div>
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
                                        id="subdomain" name="subdomain" value="{{ old('subdomain') }}"
                                        placeholder="company-name">
                                    <span class="input-group-text">.westernwatchbd.com</span>
                                </div>
                                <small class="text-muted">Use a short slug for the tenant subdomain.</small>
                                @error('subdomain')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="custom_domain" class="form-label">Custom Domain (White-label)</label>
                                <input type="text" class="form-control @error('custom_domain') is-invalid @enderror"
                                    id="custom_domain" name="custom_domain" value="{{ old('custom_domain') }}"
                                    placeholder="pos.company.com">
                                <small class="text-muted">Optional custom domain for the branded storefront.</small>
                                @error('custom_domain')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==========================================
                    3. Branding & Theme
                ========================================== --}}
                <div class="mt-3 card">
                    <div class="card-body">
                        <h4 class="mb-3 header-title">Branding & Theme</h4>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="logo" class="form-label">Company Logo</label>
                                <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                    id="logo" name="logo" accept="image/*">
                                <small class="text-muted">PNG, JPG, SVG up to 2MB. A preview appears instantly.</small>
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Logo Preview</label>
                                <div id="logo-preview" class="p-2 border rounded"
                                    style="min-height: 100px; background: #f8f9fa;">
                                    <div id="logo-preview-empty" class="text-center">
                                        <i class="ti ti-photo text-muted" style="font-size: 2rem;"></i>
                                        <p class="mb-0 text-muted small">No logo selected</p>
                                    </div>
                                    <div id="logo-preview-filled" class="d-none text-center">
                                        <img id="logo-preview-img" src="" class="img-fluid rounded"
                                            style="max-height: 110px; object-fit: contain;" alt="Logo Preview">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="favicon" class="form-label">Favicon</label>
                                <input type="file" class="form-control @error('favicon') is-invalid @enderror"
                                    id="favicon" name="favicon" accept="image/*">
                                <small class="text-muted">Used for browser tabs and bookmarks. PNG/ICO/SVG up to
                                    512KB.</small>
                                @error('favicon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Favicon Preview</label>
                                <div id="favicon-preview"
                                    class="p-2 border rounded d-flex align-items-center justify-content-center"
                                    style="min-height: 72px; background: #f8f9fa;">
                                    <i class="ti ti-browser text-muted" style="font-size: 1.6rem;"></i>
                                    <span class="ms-2 text-muted small">No favicon selected</span>
                                </div>
                            </div>

                            <div class="mb-3 col-12">
                                <label for="primary_color" class="form-label">Primary Brand Color</label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="color"
                                        class="form-control form-control-color @error('primary_color') is-invalid @enderror"
                                        id="primary_color" name="primary_color"
                                        value="{{ old('primary_color', '#2563eb') }}">
                                    <span id="primary-color-badge" class="badge px-3 py-2 text-white"
                                        style="background-color: {{ old('primary_color', '#2563eb') }};">
                                        {{ old('primary_color', '#2563eb') }}
                                    </span>
                                </div>
                                @error('primary_color')
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
                                            {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
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
                                    <option value="trial" {{ old('status') == 'trial' ? 'selected' : '' }}>Trial</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                    <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>
                                        Suspended</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="currency" class="form-label">Default Currency</label>
                                <select class="form-select @error('currency') is-invalid @enderror" id="currency"
                                    name="currency">
                                    <option value="BDT" {{ old('currency') == 'BDT' ? 'selected' : '' }}>BDT -
                                        Bangladeshi Taka</option>
                                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - US
                                        Dollar</option>
                                    <option value="INR" {{ old('currency') == 'INR' ? 'selected' : '' }}>INR - Indian
                                        Rupee</option>
                                    {{-- ✅ V2 Addition: EUR --}}
                                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro
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
                                    <option value="Asia/Dhaka" {{ old('timezone') == 'Asia/Dhaka' ? 'selected' : '' }}>
                                        Asia/Dhaka (GMT+6)</option>
                                    <option value="Asia/Kolkata"
                                        {{ old('timezone') == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (GMT+5:30)
                                    </option>
                                    <option value="UTC" {{ old('timezone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    {{-- ✅ V2 Addition: America/New_York --}}
                                    <option value="America/New_York"
                                        {{ old('timezone') == 'America/New_York' ? 'selected' : '' }}>America/New_York
                                        (EST)</option>
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
                                    placeholder="Enter full address">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                    id="city" name="city" value="{{ old('city') }}" placeholder="Enter city">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                    id="country" name="country" value="{{ old('country', 'Bangladesh') }}"
                                    placeholder="Enter country">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="zip_code" class="form-label">Zip / Postal Code</label>
                                <input type="text" class="form-control @error('zip_code') is-invalid @enderror"
                                    id="zip_code" name="zip_code" value="{{ old('zip_code') }}"
                                    placeholder="Enter zip code">
                                @error('zip_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==========================================
                    4. Industry-Specific Settings (Dynamic) - ✅ V1 Core Feature
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
                                    <option value="1">Yes, Mandatory</option>
                                    <option value="0">No, Not required</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Track Batch/Lot Numbers?</label>
                                <select class="form-select" name="settings[track_batch]">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
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
                                        value="1" checked>
                                    <label class="form-check-label">Size (S, M, L, XL)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="settings[enable_color]"
                                        value="1" checked>
                                    <label class="form-check-label">Color</label>
                                </div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Default Unit for Items</label>
                                <select class="form-select" name="settings[default_unit]">
                                    <option value="pieces">Pieces (pcs)</option>
                                    <option value="pairs">Pairs (for footwear)</option>
                                    <option value="meters">Meters (for fabrics)</option>
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
                                    <option value="1">Yes, Mandatory</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Default Warranty Period (Months)</label>
                                <input type="number" class="form-control" name="settings[default_warranty_months]"
                                    value="12" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==========================================
                    Action Buttons
                ========================================== --}}
                <div class="mt-3 card">
                    <div class="card-body text-end">
                        <a href="{{ route('superadmin.companies.index') }}" id="cancel-btn"
                            class="btn btn-secondary me-2">
                            <i class="ti ti-x me-1"></i> Cancel
                        </a>
                        <button type="submit" id="submit-btn" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Save Company
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>

    {{-- Logo Crop Modal --}}
    <div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">লোগো ক্রপ করুন</h5> {{-- ✅ V2: Bengali Title --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div style="max-height: 400px; overflow: hidden; background: #222;">
                        <img id="crop-image" src="" alt="Crop preview" style="max-width: 100%; display: block;">
                    </div>
                    {{-- ✅ V2 Addition: Bengali Instructions --}}
                    <p class="text-muted small mt-2 mb-0">ছবির উপর মাউস/আঙুল দিয়ে টেনে (drag) পজিশন ঠিক করুন, নিচের বাটন
                        দিয়ে জুম বা ঘোরান।</p>
                </div>
                <div class="modal-footer flex-wrap justify-content-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="crop-zoom-out"
                        title="Zoom Out"><i class="ti ti-zoom-out"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="crop-zoom-in" title="Zoom In"><i
                            class="ti ti-zoom-in"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="crop-rotate-left"
                        title="Rotate Left"><i class="ti ti-rotate-2"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="crop-rotate-right"
                        title="Rotate Right"><i class="ti ti-rotate-clockwise-2"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="crop-reset" title="Reset"><i
                            class="ti ti-refresh"></i></button>
                    <div class="w-100"></div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল</button>
                    {{-- ✅ V2: Bengali --}}
                    <button type="button" class="btn btn-primary" id="crop-save-btn"><i class="ti ti-check me-1"></i>
                        সেভ করুন</button> {{-- ✅ V2: Bengali --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    <script>
        $(document).ready(function() {
            // Toggle new admin section
            $('#toggle-new-admin').on('click', function() {
                $('#new-admin-section').toggle();
            });
            // ==========================================
            // 0. Dynamic Business Type Field Toggling (✅ V1 Core)
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
            toggleBusinessFields();
            $('#business_type_id').on('change', toggleBusinessFields);

            // ==========================================
            // 1. Draft Auto-Save / Restore (✅ V1 Robust Logic with Checkbox support)
            // ==========================================
            const DRAFT_KEY = 'company_create_form_draft';

            function saveFormDraft() {
                let data = {};
                $('#company-form').find('input, select, textarea').not('[type=file]').not('[name="_token"]').each(
                    function() {
                        let name = $(this).attr('name');
                        if (!name) return;
                        if ($(this).is(':checkbox')) {
                            data[name] = $(this).is(':checked') ? '1' : '0';
                        } else {
                            data[name] = $(this).val();
                        }
                    });
                try {
                    sessionStorage.setItem(DRAFT_KEY, JSON.stringify(data));
                } catch (e) {}
            }

            function restoreFormDraft() {
                try {
                    let raw = sessionStorage.getItem(DRAFT_KEY);
                    if (!raw) return;
                    let data = JSON.parse(raw);
                    Object.keys(data).forEach(function(name) {
                        let field = $('#company-form').find('[name="' + name + '"]');
                        if (field.length && !field.val() && data[name]) {
                            if (field.is(':checkbox')) {
                                field.prop('checked', data[name] === '1');
                            } else {
                                field.val(data[name]);
                            }
                        }
                    });
                } catch (e) {}
            }

            function clearFormDraft() {
                try {
                    sessionStorage.removeItem(DRAFT_KEY);
                } catch (e) {}
            }
            restoreFormDraft();

            // ==========================================
            // 2. Auto-generate slug
            // ==========================================
            if ($('#slug').val()) $('#slug').data('manual', true);
            $('#name').on('input', function() {
                if (!$('#slug').data('manual')) {
                    $('#slug').val($(this).val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(
                        /^-+|-+$/g, ''));
                }
            });
            $('#slug').on('input', function() {
                $(this).data('manual', $(this).val().trim() !== '');
            });

            // ==========================================
            // 3. Logo Upload + Crop (✅ V2 Improved Validation & Fallback)
            // ==========================================
            const logoInput = document.getElementById('logo');
            const cropImage = document.getElementById('crop-image');
            const cropModalEl = document.getElementById('cropModal');
            const cropModal = new bootstrap.Modal(cropModalEl);
            let cropper = null;
            let cropConfirmed = false;

            // ✅ V2 Additions: Strict type checking and fallback
            const maxSizeBytes = 2 * 1024 * 1024;
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'];

            function showEmptyLogoPreview() {
                $('#logo-preview-empty').removeClass('d-none');
                $('#logo-preview-filled').addClass('d-none').removeClass('d-flex');
            }

            function showFilledLogoPreview(url) {
                $('#logo-preview-img').attr('src', url);
                $('#logo-preview-img-small').attr('src', url);
                $('#logo-preview-empty').addClass('d-none');
                $('#logo-preview-filled').removeClass('d-none').addClass('d-flex');
            }

            function resetLogoInput() {
                $(logoInput).val('');
                showEmptyLogoPreview();
            }

            $(logoInput).on('change', function(e) {
                const file = e.target.files[0];
                if (!file) {
                    showEmptyLogoPreview();
                    return;
                }

                // ✅ V2: Size validation
                if (file.size > maxSizeBytes) {
                    alert('লোগোর সাইজ 2MB এর বেশি হতে পারবে না। অনুগ্রহ করে ছোট সাইজের ফাইল সিলেক্ট করুন।');
                    resetLogoInput();
                    return;
                }

                // ✅ V2: Type validation
                if (!allowedTypes.includes(file.type)) {
                    alert('শুধুমাত্র PNG, JPG অথবা SVG ফরম্যাটের ছবি আপলোড করা যাবে।');
                    resetLogoInput();
                    return;
                }

                if (file.type === 'image/svg+xml') {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        showFilledLogoPreview(ev.target.result);
                    };
                    reader.readAsDataURL(file);
                    return;
                }

                // ✅ V2: Cropper.js fallback if library fails to load
                if (typeof Cropper === 'undefined') {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        showFilledLogoPreview(ev.target.result);
                    };
                    reader.readAsDataURL(file);
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(ev) {
                    cropImage.src = ev.target.result;
                    cropConfirmed = false;
                    cropModal.show();
                };
                reader.readAsDataURL(file);
            });

            $(cropModalEl).on('shown.bs.modal', function() {
                if (cropper) cropper.destroy();
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    cropBoxResizable: false,
                    cropBoxMovable: false,
                    background: false,
                    guides: false
                });
            });

            $(cropModalEl).on('hidden.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                if (!cropConfirmed) resetLogoInput();
            });

            $('#crop-zoom-in').on('click', () => cropper && cropper.zoom(0.1));
            $('#crop-zoom-out').on('click', () => cropper && cropper.zoom(-0.1));
            $('#crop-rotate-left').on('click', () => cropper && cropper.rotate(-45));
            $('#crop-rotate-right').on('click', () => cropper && cropper.rotate(45));
            $('#crop-reset').on('click', () => cropper && cropper.reset());

            $('#crop-save-btn').on('click', function() {
                if (!cropper) return;
                cropper.getCroppedCanvas({
                    width: 400,
                    height: 400,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                }).toBlob(function(blob) {
                    if (!blob) return;
                    const baseName = logoInput.files[0].name.replace(/\.[^/.]+$/, '');
                    const croppedFile = new File([blob], baseName + '.png', {
                        type: 'image/png'
                    });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedFile);
                    logoInput.files = dataTransfer.files;
                    showFilledLogoPreview(URL.createObjectURL(blob));
                    cropConfirmed = true;
                    cropModal.hide();
                }, 'image/png');
            });

            $('#recrop-btn').on('click', function() {
                const currentFile = logoInput.files[0];
                if (!currentFile || currentFile.type === 'image/svg+xml') return;
                const reader = new FileReader();
                reader.onload = function(ev) {
                    cropImage.src = ev.target.result;
                    cropConfirmed = false;
                    cropModal.show();
                };
                reader.readAsDataURL(currentFile);
            });

            // ==========================================
            // 4. Favicon Preview + Theme Color Badge
            // ==========================================
            $('#favicon').on('change', function(e) {
                const file = e.target.files[0];
                const preview = $('#favicon-preview');

                if (!file) {
                    preview.html(
                        '<i class="ti ti-browser text-muted" style="font-size: 1.6rem;"></i><span class="ms-2 text-muted small">No favicon selected</span>'
                        );
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.html(
                        `<img src="${ev.target.result}" alt="Favicon Preview" width="32" height="32" style="object-fit: contain;"> <span class="ms-2 text-muted small">New favicon</span>`
                        );
                };
                reader.readAsDataURL(file);
            });

            $('#primary_color').on('input change', function() {
                const color = $(this).val();
                $('#primary-color-badge').css('background-color', color).text(color);
            });

            // ==========================================
            // 5. Plan Details Display (✅ V1 Clean Logic, avoided V2 duplication)
            // ==========================================
            $('#plan_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                if ($(this).val() && selectedOption.data('price')) {
                    $('#plan-info').html(`
                        <div class="mt-1">
                            <strong>Price:</strong> $${parseFloat(selectedOption.data('price')).toFixed(2)}/month<br>
                            <strong>Trial Period:</strong> ${selectedOption.data('trial')} days<br>
                            <strong>User Limit:</strong> ${selectedOption.data('users')} users<br>
                            <strong>Branch Limit:</strong> ${selectedOption.data('branches')} branches
                        </div>
                    `);
                    $('#plan-details').show();
                } else {
                    $('#plan-details').hide();
                }
            });
            if ($('#plan_id').val()) $('#plan_id').trigger('change');

            // ==========================================
            // 5. Live Validation (✅ V2 Addition: Blur Event)
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
            // 6. Live Error Clearing & Draft Save
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
                if (this.type !== 'file') saveFormDraft();
            });

            // ==========================================
            // 7. AJAX Form Submission with Smooth Scroll (✅ V2 Addition: Scroll to error)
            // ==========================================
            $('#company-form').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let formData = new FormData(this);
                let submitBtn = $('#submit-btn');

                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Saving...');
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').text('').hide();

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        clearFormDraft();
                        toastr.success(response.message || 'Company saved successfully!',
                            'Success');
                        setTimeout(() => {
                            window.location.href = response.redirect ||
                                '{{ route('superadmin.companies.index') }}';
                        }, 1200);
                    },
                    error: function(xhr) {
                        saveFormDraft();
                        if (xhr.status === 422) {
                            let firstErrorElement = null;
                            $.each(xhr.responseJSON.errors, function(key, messages) {
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
                                    if (!feedback.length) {
                                        let target = input.parent().hasClass(
                                            'input-group') ? input.parent() : input;
                                        target.after(
                                            '<div class="invalid-feedback" style="display:block;">' +
                                            messages[0] + '</div>');
                                    } else {
                                        feedback.text(messages[0]).show().css('display',
                                            'block');
                                    }
                                    if (!firstErrorElement) firstErrorElement = input;
                                }
                            });

                            // ✅ V2 Addition: Smooth scroll to first error
                            if (firstErrorElement) {
                                $('html, body').animate({
                                    scrollTop: firstErrorElement.offset().top - 150
                                }, 500);
                            }
                        } else {
                            toastr.error('An unexpected error occurred. Please try again.',
                                'Error');
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(
                            '<i class="ti ti-device-floppy me-1"></i> Save Company');
                    }
                });
            });

            $('#cancel-btn').on('click', clearFormDraft);
        });
    </script>
@endpush
