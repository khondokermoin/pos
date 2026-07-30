<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyScope implements \Illuminate\Database\Eloquent\Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Safety: skip when running in console
        if (app()->runningInConsole()) {
            return;
        }

        // Ensure a user is present before applying tenant filter
        if (! auth()->check()) {
            return;
        }

        $user = auth()->user();

        // Super Admin bypass
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return;
        }

        // If user has no company assigned, do not apply scope
        if (! $user->company_id) {
            return;
        }

        // Apply company filter
        $builder->where($model->getTable() . '.company_id', $user->company_id);
    }
}
