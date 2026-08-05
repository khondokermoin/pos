<?php

namespace App\Http\Middleware;

use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenantByDomain
{
    public function __construct(protected TenantService $tenantService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        if (empty($host)) {
            $this->bindTenant(null);

            return $next($request);
        }

        try {
            // Resolve tenant from host using TenantService (with cache)
            $company = $this->tenantService->resolveFromHost($host);

            $this->bindTenant($company);

            if ($company) {
                // Also bind TenantService so it's accessible with resolved tenant
                app()->instance(TenantService::class, $this->tenantService);
            }
        } catch (\Throwable $e) {
            // If tenant resolution fails (e.g. DB connection issue), log and continue
            // Super-admin and other routes should still work normally
            $this->bindTenant(null);
            report($e);
        }

        return $next($request);
    }

    /**
     * Bind the resolved tenant (or null) to the container as 'tenant'.
     *
     * NOTE: app()->instance('tenant', null) does NOT work — the container's
     * resolve() checks isset($this->instances['tenant']), and isset() is
     * false for a null value, so it falls through to trying to instantiate
     * "tenant" as a class name. bind() with a closure avoids that pitfall.
     */
    protected function bindTenant(?\App\Models\Company $company): void
    {
        app()->bind('tenant', fn () => $company);
    }
}
