<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }} | Login</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login">
    <meta name="author" content="Coderthemes">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('frontend_assets/images/favicon.ico') }}">

    <!-- Theme Config -->
    <script src="{{ asset('frontend_assets/js/config.js') }}"></script>

    <!-- CSS -->
    <link href="{{ asset('frontend_assets/css/vendor.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend_assets/css/app.min.css') }}" rel="stylesheet" id="app-style">
    <link href="{{ asset('frontend_assets/css/icons.min.css') }}" rel="stylesheet">
</head>

<body>

<div class="auth-bg d-flex min-vh-100 justify-content-center align-items-start">

    <div class="m-3 row g-0 justify-content-center w-100 m-xxl-5 px-xxl-4">

        <div class="col-xl-4 col-lg-5 col-md-6">

            <div class="card p-3 p-xxl-4 text-center overflow-hidden">

                <a href="{{ url('/') }}" class="auth-brand mb-4">
                    <img src="{{ asset('frontend_assets/images/logo-dark.png') }}"
                        class="logo-dark"
                        height="26"
                        alt="Logo">

                    <img src="{{ asset('frontend_assets/images/logo.png') }}"
                        class="logo-light"
                        height="26"
                        alt="Logo">
                </a>

                <h4 class="fw-semibold fs-18 mb-2">
                    Log In to your account
                </h4>

                <p class="text-muted mb-4">
                    Enter your email and password to continue.
                </p>

                {{-- Success Message --}}
                @if(session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger text-start">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="text-start">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Email Address
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="Enter your email"
                            required
                            autofocus
                            autocomplete="username">

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password
                        </label>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password">

                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Remember --}}
                    <div class="mb-3 d-flex justify-content-between align-items-center">

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="remember"
                                name="remember"
                                {{ old('remember') ? 'checked' : '' }}>

                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>

                        </div>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-muted border-bottom border-dashed">
                                Forgot Password?
                            </a>
                        @endif

                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-semibold">
                            Log In
                        </button>
                    </div>

                </form>

            </div>

            <div class="text-center mt-3">

                @if (Route::has('register'))
                    <p class="fs-14 mb-4">
                        Don't have an account?

                        <a href="{{ route('register') }}"
                           class="fw-semibold text-danger ms-1">
                            Sign Up!
                        </a>
                    </p>
                @endif

                <p class="mb-0">
                    &copy; {{ date('Y') }}
                    Zircos —
                    By
                    <a href="https://coderthemes.com/"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="fw-bold text-decoration-underline text-uppercase text-reset fs-12">
                        Coderthemes
                    </a>
                </p>

            </div>

        </div>

    </div>

</div>

<script src="{{ asset('frontend_assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('frontend_assets/js/app.js') }}"></script>

</body>
</html>