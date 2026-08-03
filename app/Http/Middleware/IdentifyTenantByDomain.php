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
            return $next($request);
        }

        try {
            // Resolve tenant from host using TenantService (with cache)
            $company = $this->tenantService->resolveFromHost($host);

            if ($company) {
                // Bind tenant to IoC container - accessible anywhere via app('tenant')
                app()->instance('tenant', $company);

                // Also bind TenantService so it's accessible with resolved tenant
                app()->instance(TenantService::class, $this->tenantService);
            }
        } catch (\Throwable $e) {
            // If tenant resolution fails (e.g. DB connection issue), log and continue
            // Super-admin and other routes should still work normally
            report($e);
        }

        return $next($request);
    }
}
