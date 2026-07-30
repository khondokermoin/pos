<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    protected array $except = [
        'company/subscription',
        'company/subscription/plans',
        'company/subscription/subscribe/*',
        'company/subscription/payment/callback',
        'company/announcements',
        'company/settings/profile',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('Company Admin')) {
            return $next($request);
        }

        if ($request->session()->get('impersonated_by') || $request->session()->get('impersonator_id')) {
            return $next($request);
        }

        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        $company = $user->company;

        if (!$company) {
            return $next($request);
        }

        if ($company->status === 'trial' && $company->trial_ends_at && $company->trial_ends_at->isFuture()) {
            return $next($request);
        }

        $subscription = $company->subscription;

        if ($subscription && $subscription->status === 'active' && !$subscription->isExpired()) {
            return $next($request);
        }

        $message = match (true) {
            !$subscription => 'You need an active subscription to access this feature.',
            $subscription->status === 'expired' => 'Your subscription has expired. Please renew to continue.',
            $subscription->status === 'suspended' => 'Your subscription has been suspended. Please contact support.',
            $subscription->status === 'cancelled' => 'Your subscription has been cancelled. Please subscribe to a new plan.',
            $subscription->isExpired() => 'Your subscription has expired. Please renew to continue.',
            default => 'Please subscribe to a plan to access this feature.',
        };

        return redirect()->route('company.subscription.index')
            ->with('subscription_warning', $message);
    }
}
