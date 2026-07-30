@extends('layouts.admin_master')

@section('title', 'Email & SMS - Global Settings')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Email & SMS Configuration</h2>
                <div class="mt-1 text-muted">Setup your mail server credentials and SMS gateway for notifications.</div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-lg-8">

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

                <form action="{{ route('superadmin.settings.email.update') }}" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="group" value="email">

                    <div class="mb-4 card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ti ti-mail me-2"></i> SMTP Mail Configuration</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Mail Mailer -->
                                <div class="col-md-6">
                                    <label class="form-label required">Mail Mailer</label>
                                    <select name="mail_mailer" class="form-select @error('mail_mailer') is-invalid @enderror">
                                        <option value="smtp" {{ old('mail_mailer', $settings['mail_mailer'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="mailgun" {{ old('mail_mailer', $settings['mail_mailer'] ?? '') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                        <option value="log" {{ old('mail_mailer', $settings['mail_mailer'] ?? '') == 'log' ? 'selected' : '' }}>Log (Testing)</option>
                                    </select>
                                    @error('mail_mailer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Mail Host -->
                                <div class="col-md-6">
                                    <label class="form-label required">SMTP Host</label>
                                    <input type="text" name="mail_host" class="form-control @error('mail_host') is-invalid @enderror"
                                        value="{{ old('mail_host', $settings['mail_host'] ?? 'smtp.mailtrap.io') }}" placeholder="smtp.example.com">
                                    @error('mail_host') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Mail Port -->
                                <div class="col-md-4">
                                    <label class="form-label required">SMTP Port</label>
                                    <input type="number" name="mail_port" class="form-control @error('mail_port') is-invalid @enderror"
                                        value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}" placeholder="587">
                                    @error('mail_port') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Mail Encryption -->
                                <div class="col-md-4">
                                    <label class="form-label required">Encryption</label>
                                    <select name="mail_encryption" class="form-select @error('mail_encryption') is-invalid @enderror">
                                        <option value="tls" {{ old('mail_encryption', $settings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ old('mail_encryption', $settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="null" {{ old('mail_encryption', $settings['mail_encryption'] ?? '') == 'null' ? 'selected' : '' }}>None</option>
                                    </select>
                                    @error('mail_encryption') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Mail Username -->
                                <div class="col-md-4">
                                    <label class="form-label">SMTP Username</label>
                                    <input type="text" name="mail_username" class="form-control @error('mail_username') is-invalid @enderror"
                                        value="{{ old('mail_username', $settings['mail_username'] ?? '') }}">
                                    @error('mail_username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Mail Password -->
                                <div class="col-md-6">
                                    <label class="form-label">SMTP Password</label>
                                    <input type="password" name="mail_password" class="form-control @error('mail_password') is-invalid @enderror"
                                        value="{{ old('mail_password', $settings['mail_password'] ?? '') }}" placeholder="Leave blank to keep current">
                                    @error('mail_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- From Address -->
                                <div class="col-md-6">
                                    <label class="form-label required">From Email Address</label>
                                    <input type="email" name="mail_from_address" class="form-control @error('mail_from_address') is-invalid @enderror"
                                        value="{{ old('mail_from_address', $settings['mail_from_address'] ?? 'noreply@yourdomain.com') }}">
                                    @error('mail_from_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ti ti-message me-2"></i> SMS Gateway Configuration (Optional)</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">SMS Provider</label>
                                    <select name="sms_provider" class="form-select">
                                        <option value="none" {{ old('sms_provider', $settings['sms_provider'] ?? 'none') == 'none' ? 'selected' : '' }}>None</option>
                                        <option value="twilio" {{ old('sms_provider', $settings['sms_provider'] ?? '') == 'twilio' ? 'selected' : '' }}>Twilio</option>
                                        <option value="msg91" {{ old('sms_provider', $settings['sms_provider'] ?? '') == 'msg91' ? 'selected' : '' }}>MSG91</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">API Key / Auth Token</label>
                                    <input type="password" name="sms_api_key" class="form-control"
                                        value="{{ old('sms_api_key', $settings['sms_api_key'] ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Save Email & SMS Settings
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
