<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @routes
    @viteReactRefresh
    @if (app()->environment('local'))
        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @elseif (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/slick.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
        <script src="{{ asset('assets/js/vendor/jquery.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap-bundle.js') }}"></script>
        <script src="{{ asset('assets/js/slick.js') }}"></script>
    @endif
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia

    {{-- JS libraries are bundled via Vite/npm - no separate script tags needed --}}
</body>

</html>
