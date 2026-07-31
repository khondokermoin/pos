<!-- Character Encoding -->
<meta charset="UTF-8">

<!-- Responsive Meta -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Page Title -->
<title>@yield('title', 'Dashboard') | Zircos Admin Panel</title>

<!-- SEO Meta -->
<meta name="description" content="Professional Admin Dashboard for CRM, ERP, CMS, HRM and Business Management Systems.">
<meta name="keywords" content="Admin Dashboard, CRM, ERP, CMS, Laravel Admin, Bootstrap Dashboard">
<meta name="author" content="Coderthemes">

<!-- Browser Theme -->
<meta name="theme-color" content="#3b82f6">
<meta name="robots" content="index,follow">

<!-- CSRF Token (Laravel) -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ asset('frontend_assets/images/favicon.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('frontend_assets/images/favicon.ico') }}">

<!-- Preconnect for Faster CDN Loading -->
<link rel="preconnect" href="https://cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">

<!-- Theme Configuration -->
<script src="{{ asset('frontend_assets/js/config.js') }}"></script>

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Vendor CSS -->
<link rel="stylesheet" href="{{ asset('frontend_assets/css/vendor.min.css') }}">

<!-- Application CSS -->
<link rel="stylesheet" href="{{ asset('frontend_assets/css/app.min.css') }}" id="app-style">

<!-- Icons -->
<link rel="stylesheet" href="{{ asset('frontend_assets/css/icons.min.css') }}">

<!-- Sidebar Tree-View Indicator Styles -->
<link rel="stylesheet" href="{{ asset('frontend_assets/css/sidebar-tree.css') }}">


<style>
    /* ─────────────────────────────────────────────────────────────────────────
       Alpine.js: x-cloak — Alpine initialize হওয়ার আগে element লুকিয়ে রাখে
    ───────────────────────────────────────────────────────────────────────── */
    [x-cloak] {
        display: none !important;
    }

    /* ─────────────────────────────────────────────────────────────────────────
       Sidebar layout margin-left fix — সব sidenav-size state-এর জন্য প্রযোজ্য।
       শুধু "default" নয়, condensed / compact / small / wide সব state-এ
       .page-content ও .app-topbar সঠিকভাবে সরে যাবে।
    ───────────────────────────────────────────────────────────────────────── */
    html:not([data-layout=topnav]):not([data-sidenav-size=full]) .page-content,
    html:not([data-layout=topnav]):not([data-sidenav-size=full]) .app-topbar {
        margin-left: var(--ct-sidenav-width);
        transition: margin-left 0.2s ease;
    }

    /* condensed / icon-only sidebar — narrower margin */
    html[data-sidenav-size=condensed]:not([data-layout=topnav]) .page-content,
    html[data-sidenav-size=condensed]:not([data-layout=topnav]) .app-topbar {
        margin-left: var(--ct-sidenav-condensed-width, 70px);
    }

    /* compact sidebar */
    html[data-sidenav-size=compact]:not([data-layout=topnav]) .page-content,
    html[data-sidenav-size=compact]:not([data-layout=topnav]) .app-topbar {
        margin-left: var(--ct-sidenav-compact-width, 160px);
    }

    /* small / slim sidebar */
    html[data-sidenav-size=small]:not([data-layout=topnav]) .page-content,
    html[data-sidenav-size=small]:not([data-layout=topnav]) .app-topbar {
        margin-left: var(--ct-sidenav-small-width, 200px);
    }

    /* ─────────────────────────────────────────────────────────────────────────
       Sidebar menu text & icon styling
    ───────────────────────────────────────────────────────────────────────── */

    /* মেনু লেখার সাইজ ও overflow নিয়ন্ত্রণ */
    .side-nav-link .menu-text {
        font-size: 12.5px;
        white-space: nowrap;
        line-height: 1.5;
        display: inline-block;
        vertical-align: middle;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* সেকশন টাইটেল */
    .side-nav-title {
        color: #aab8c5;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        padding-top: 15px;
        padding-bottom: 5px;
    }

    /* মেইন মেনু টেক্সট ও আইকন রং */
    .side-nav-link .menu-text,
    .side-nav-link .menu-icon {
        color: #cedce4;
    }

    /* Hover: সাদা */
    .side-nav-link:hover .menu-text,
    .side-nav-link:hover .menu-icon {
        color: #ffffff;
    }

    /* সাব-মেনু */
    .sub-menu .side-nav-link .menu-text {
        font-size: 12px;
        color: #98a6ad;
    }

    .sub-menu .side-nav-link:hover .menu-text {
        color: #ffffff;
    }

    /* Active মেনু */
    .side-nav-item.active>.side-nav-link .menu-text,
    .side-nav-item.active>.side-nav-link .menu-icon {
        color: #ffffff;
    }

    /* ─────────────────────────────────────────────────────────────────────────
       .select-visible — একটি utility class।
       যেসব select theme-এর CSS-এ hidden/broken হয়ে যায়,
       শুধু সেগুলোতে এই class যোগ করলেই ঠিক হবে।
       Global select.form-select override করা হচ্ছে না।
    ───────────────────────────────────────────────────────────────────────── */
    .select-visible {
        -webkit-appearance: auto !important;
        -moz-appearance: auto !important;
        appearance: auto !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        min-height: 38px !important;
        width: 100% !important;
    }

    .select-visible:focus {
        outline: 0;
    }
</style>

<!-- Additional Page Styles -->
@stack('styles')
