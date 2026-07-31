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


<style>
    /* Alpine x-cloak: Alpine initialize হওয়ার আগে element লুকিয়ে রাখে */
    [x-cloak] {
        display: none !important;
    }

    /* Fix: default sidenav-size এ .page-content এর সঠিক margin-left নিশ্চিত করা */
    html[data-sidenav-size=default]:not([data-layout=topnav]) .page-content,
    html[data-sidenav-size=default]:not([data-layout=topnav]) .app-topbar {
        margin-left: var(--ct-sidenav-width) !important;
    }

    /* সাইডবার মেনুর লেখার সাইজ বড় করার জন্য */
    .side-nav-link .menu-text {
        font-size: 12.5px !important;
        white-space: nowrap !important;
        line-height: 1.5 !important;
        display: inline-block;
        vertical-align: middle;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* সাইডবারের সেকশন টাইটেল স্পষ্ট করার জন্য */
    .side-nav-title {
        color: #aab8c5 !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: 0.8px !important;
        text-transform: uppercase !important;
        padding-top: 15px !important;
        padding-bottom: 5px !important;
    }

    /* মেইন মেনুর টেক্সট এবং আইকনের রং উজ্জ্বল করার জন্য */
    .side-nav-link .menu-text,
    .side-nav-link .menu-icon {
        color: #cedce4 !important;
    }

    /* মেনুর উপর মাউস নিলে (Hover) যেন সাদা হয়ে যায় */
    .side-nav-link:hover .menu-text,
    .side-nav-link:hover .menu-icon {
        color: #ffffff !important;
    }

    /* সাব-মেনুর (Sub-menu) লেখাগুলো সামঞ্জস্যপূর্ণ রাখার জন্য */
    .sub-menu .side-nav-link .menu-text {
        font-size: 12px !important;
        color: #98a6ad !important;
    }

    /* সাব-মেনুতে হোভার ইফেক্ট */
    .sub-menu .side-nav-link:hover .menu-text {
        color: #ffffff !important;
    }

    /* অ্যাকটিভ মেনুর কালার হাইলাইট করার জন্য */
    .side-nav-item.active>.side-nav-link .menu-text,
    .side-nav-item.active>.side-nav-link .menu-icon {
        color: #ffffff !important;
    }
</style>

<!-- Additional Page Styles -->
@stack('styles')
