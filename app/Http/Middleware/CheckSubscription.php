<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    // =========================================================================
    // SUBSCRIPTION ENFORCEMENT MIDDLEWARE
    // =========================================================================
    //
    // PURPOSE:
    //   Block Company Admin users from accessing the company panel if their
    //   subscription is expired, suspended, or cancelled. Redirect them to
    //   the subscription pricing page so they can renew.
    //
    // CRITICAL — REDIRECT LOOP PREVENTION:
    //   The $except list MUST include every route that is part of the
    //   subscription / payment flow. If any of these routes are NOT excluded,
    //   the middleware will redirect the user back to the subscription page
    //   while they are already ON the subscription page, creating an infinite
    //   redirect loop (ERR_TOO_MANY_REDIRECTS).
    //
    //   Routes that MUST be excluded:
    //     1. company/subscription          — the pricing/status page itself
    //     2. company/subscription/plans    — alias for the pricing page
    //     3. company/subscription/subscribe/*  — the checkout POST handler
    //     4. company/subscription/invoice/*/download — invoice PDF download
    //     5. company/subscription/payment/callback — SSLCommerz POST callback
    //        (this route is actually OUTSIDE the auth middleware group, but
    //         we include it here as a belt-and-suspenders safety measure)
    //     6. payment/result                — public payment result page
    //     7. company/announcements         — always accessible (UX requirement)
    //     8. company/settings/profile      — always accessible (UX requirement)
    //
    // IMPERSONATION BYPASS:
    //   Super Admins impersonating a company should never be blocked by this
    //   middleware — they need full access to diagnose issues.
    // =========================================================================

    /**
     * URL patterns that are ALWAYS allowed through, regardless of subscription status.
     *
     * Uses Laravel's $request->is() which supports '*' wildcards.
     * Patterns are relative to the app root (no leading slash).
     */
    protected array $except = [
        // ── Subscription management pages ─────────────────────────────────
        'company/subscription',
        'company/subscription/plans',
        'company/subscription/subscribe/*',
        'company/subscription/invoice/*/download',

        // ── Payment gateway callback & result ─────────────────────────────
        // These are PUBLIC routes (outside the auth group) but we list them
        // here as a safety net in case the route group ever changes.
        'company/subscription/payment/callback',
        'payment/result',

        // ── Always-accessible company pages ───────────────────────────────
        'company/announcements',
        'company/settings/profile',
        'company/settings/profile/update',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // ── 1. Only enforce for authenticated Company Admins ─────────────────
        // All other roles (Super Admin, Manager, Salesman) pass through freely.
        if (! $user || ! $user->hasRole('Company Admin')) {
            return $next($request);
        }

        // ── 2. Impersonation bypass ───────────────────────────────────────────
        // Super Admins impersonating a company tenant must never be blocked.
        // We check both session keys used by the impersonation system.
        if (
            $request->session()->get('impersonated_by') ||
            $request->session()->get('impersonator_id')
        ) {
            return $next($request);
        }

        // ── 3. Whitelist check — subscription & payment routes ────────────────
        // If the current URL matches any pattern in $except, let it through.
        // This is the primary redirect-loop prevention mechanism.
        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // ── 4. Company existence check ────────────────────────────────────────
        $company = $user->company;

        if (! $company) {
            // No company record — let them through (edge case / new account)
            return $next($request);
        }

        // ── 5. Trial period check ─────────────────────────────────────────────
        // Companies in an active trial are always allowed through.
        if (
            $company->status === 'trial' &&
            $company->trial_ends_at &&
            $company->trial_ends_at->isFuture()
        ) {
            return $next($request);
        }

        // ── 6. Active subscription check ─────────────────────────────────────
        // Fetch the latest subscription for this company.
        // We use the relationship defined on the Company model.
        $subscription = $company->subscription;

        if ($subscription && $subscription->status === 'active' && ! $subscription->isExpired()) {
            return $next($request);
        }

        // ── 7. Build a human-readable message and redirect ────────────────────
        // At this point the company has no valid subscription. Redirect them
        // to the subscription pricing page with an appropriate warning message.
        $message = match (true) {
            ! $subscription
            => 'You need an active subscription to access this feature. Please choose a plan to get started.',
            $subscription->status === 'expired' || $subscription->isExpired()
            => 'Your subscription has expired. Please renew your plan to continue using the system.',
            $subscription->status === 'suspended'
            => 'Your subscription has been suspended. Please contact support to reactivate your account.',
            $subscription->status === 'cancelled'
            => 'Your subscription has been cancelled. Please subscribe to a new plan to regain access.',
            default
            => 'Please subscribe to a plan to access this feature.',
        };

        return redirect()->route('company.subscription.index')
            ->with('subscription_warning', $message);
    }
}
