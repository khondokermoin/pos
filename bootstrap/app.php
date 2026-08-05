<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__ . '/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ─────────────────────────────────────────────────────────────────────
        // CSRF EXCEPTIONS — SSLCommerz Payment Gateway Callbacks
        //
        // SSLCommerz POSTs to these URLs directly from their payment servers.
        // These are cross-origin server-to-server (or browser redirect) POSTs
        // that never carry a Laravel CSRF token. They MUST be excluded or every
        // payment callback will return a 419 "Page Expired" error.
        //
        // Routes excluded:
        //   • company/subscription/payment/callback  — main success/fail/cancel POST
        //                                              from SSLCommerz servers (cross-origin,
        //                                              no session cookie present)
        //   • payment/result                         — GET result page (safe, no state change)
        //
        // REMOVED: company/subscription/subscribe/* was previously excluded but this is
        // WRONG — subscribe is an authenticated, user-initiated POST inside the auth group.
        // SSLCommerz never POSTs to it; only the user's browser does. Excluding it opened
        // a CSRF hole where a hostile page could force a billing redirect on a logged-in
        // Company Admin. The CSRF token is present in the Blade form, so no exemption needed.
        // ─────────────────────────────────────────────────────────────────────
        $middleware->validateCsrfTokens(except: [
            'company/subscription/payment/callback',
            'payment/result',
        ]);

        $middleware->web(append: [
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            // Tenant domain resolution runs on every web request
            // It reads the host header and resolves the company from DB/cache
            \App\Http\Middleware\IdentifyTenantByDomain::class,
            // Inertia middleware shares tenant branding + auth + flash to React
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);


        // Spatie Middleware Aliases
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'tenant.access' => \App\Http\Middleware\EnsureTenantAccess::class,
            'tenant.domain' => \App\Http\Middleware\IdentifyTenantByDomain::class,
            'inertia' => \App\Http\Middleware\HandleInertiaRequests::class,
            'subscription.check' => \App\Http\Middleware\CheckSubscription::class,
        ]);

        // Guest Redirect Logic (SaaS Roles)
        // লগইন করা ইউজার যদি /login পেজে আসে, তাকে তার রোল অনুযায়ী ড্যাশবোর্ডে পাঠানো হবে
        $middleware->redirectUsersTo(function (Request $request) {

            $user = $request->user();

            if ($user) {
                if ($user->hasRole('Super Admin')) {
                    return route('superadmin.dashboard');
                } elseif ($user->hasRole('Company Admin')) {
                    return route('company.dashboard');
                } elseif ($user->hasRole('Manager') || $user->hasRole('Salesman')) { // Fixed: Manager & Salesman
                    return route('branch.dashboard');
                }
            }

            return route('dashboard'); // Fallback
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
