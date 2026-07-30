<?php

namespace App\Http\Middleware;

use App\Services\TenantService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        // Resolve TenantService from container
        $tenantService = app(TenantService::class);

        return [
            ...parent::share($request),

            // Auth user data
            'auth' => [
                'user' => $request->user() ? [
                    'id'         => $request->user()->id,
                    'name'       => $request->user()->name,
                    'email'      => $request->user()->email,
                    'company_id' => $request->user()->company_id,
                    'branch_id'  => $request->user()->branch_id,
                    'roles'      => $request->user()->getRoleNames(),
                    'avatar_url' => null,
                ] : null,
            ],

            // Tenant branding data - this is the "magic" for custom domains
            // React reads this to show the correct logo, colors, company name
            'tenant' => fn() => $tenantService->getBrandingData(),

            // Flash messages for toast notifications
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error'   => fn() => $request->session()->get('error'),
                'warning' => fn() => $request->session()->get('warning'),
                'info'    => fn() => $request->session()->get('info'),
            ],

            // App-level config for React
            'app' => [
                'name'    => config('app.name'),
                'url'     => config('app.url'),
                'locale'  => app()->getLocale(),
                'version' => config('app.version', '1.0.0'),
            ],
        ];
    }
}
