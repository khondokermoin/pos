<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized access.');
        }

        // Super Admin bypasses tenant restrictions
        if ($user->hasRole('Super Admin')) {
            return $next($request);
        }

        // Ensure user is assigned to a company
        if (! $user->company_id) {
            abort(403, 'Tenant access requires a company assignment.');
        }

        $route = $request->route();
        $params = $route ? $route->parameters() : [];

        // If route has a company model parameter, ensure it matches the user's company
        if (isset($params['company']) && $params['company'] instanceof \App\Models\Company) {
            if ($params['company']->id !== $user->company_id) {
                abort(403, 'Company access forbidden.');
            }
        }

        // Branch-specific checks: branch users must be assigned a branch and can only access their branch
        if ($user->hasRole('Manager') || $user->hasRole('Salesman')) {
            if (! $user->branch_id) {
                abort(403, 'Branch users must be assigned to a branch.');
            }

            if (isset($params['branch']) && $params['branch'] instanceof \App\Models\Branch) {
                if ($params['branch']->id !== $user->branch_id || $params['branch']->company_id !== $user->company_id) {
                    abort(403, 'Branch access forbidden.');
                }
            }
        } else {
            // For other user roles, if a branch is present in the route ensure it belongs to same company
            if (isset($params['branch']) && $params['branch'] instanceof \App\Models\Branch) {
                if ($params['branch']->company_id !== $user->company_id) {
                    abort(403, 'Branch access forbidden.');
                }
            }
        }

        return $next($request);
    }
}
