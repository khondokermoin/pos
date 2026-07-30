<?php

namespace App\Observers;

use App\Models\Company;
use App\Services\TenantProvisioningService;
use App\Services\TenantService;

class CompanyObserver
{
    /**
     * When a new company is created:
     * 1. Provision default data (units, taxes, walk-in customer, attributes)
     */
    public function created(Company $company): void
    {
        resolve(TenantProvisioningService::class)->provision($company);
    }

    /**
     * When a company is updated (logo, domain, theme changed):
     * Clear the tenant cache so the next request gets fresh branding data.
     * This ensures React frontend shows updated logo/colors immediately.
     */
    public function updated(Company $company): void
    {
        // Only clear cache if domain or branding fields changed
        $watchedFields = [
            'custom_domain',
            'subdomain',
            'logo',
            'favicon',
            'theme_settings',
            'social_links',
            'contact_info',
            'name',
            'status',
            'currency',
            'timezone',
        ];

        $changed = array_intersect(array_keys($company->getDirty()), $watchedFields);

        if (! empty($changed)) {
            resolve(TenantService::class)->clearCache($company);
        }
    }

    /**
     * When a company is deleted or suspended:
     * Clear the tenant cache immediately so the domain stops resolving.
     */
    public function deleted(Company $company): void
    {
        resolve(TenantService::class)->clearCache($company);
    }

    /**
     * When a soft-deleted company is restored:
     * Clear cache so the domain starts resolving again.
     */
    public function restored(Company $company): void
    {
        resolve(TenantService::class)->clearCache($company);
    }
}
