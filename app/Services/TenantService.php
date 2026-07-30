<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * TenantService - Central service for multi-tenant domain resolution.
 *
 * How it works:
 * 1. A request comes in for pos.customer-brand.com
 * 2. This service looks up the company by custom_domain or subdomain
 * 3. The company is cached in Redis/file cache for 60 minutes
 * 4. The company is bound to the IoC container as 'tenant'
 * 5. React frontend receives branding data via Inertia SharedProps
 */
class TenantService
{
    protected ?Company $tenant = null;

    /**
     * Resolve the tenant from the incoming host string.
     * Uses cache to avoid repeated DB lookups on every request.
     */
    public function resolveFromHost(string $host): ?Company
    {
        $host = strtolower(trim($host));

        // Cache key based on host - avoids DB hit on every request
        $cacheKey = 'tenant_by_host_' . md5($host);

        $companyId = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($host) {
            $subdomain = $this->extractSubdomain($host);

            try {
                $company = Company::query()
                    ->where('status', 'active')
                    ->where(function ($query) use ($host, $subdomain) {
                        $query->where('custom_domain', $host)
                            ->orWhere('subdomain', $subdomain);
                    })
                    ->select('id')
                    ->first();

                return $company?->id;
            } catch (Throwable $e) {
                report($e);

                return null;
            }
        });

        if (! $companyId) {
            return null;
        }

        // Load full company with fresh data (not cached to allow real-time updates)
        $this->tenant = Cache::remember('tenant_data_' . $companyId, now()->addMinutes(30), function () use ($companyId) {
            try {
                return Company::query()
                    ->where('id', $companyId)
                    ->where('status', 'active')
                    ->first();
            } catch (Throwable $e) {
                report($e);

                return null;
            }
        });

        return $this->tenant;
    }

    /**
     * Get the currently resolved tenant.
     */
    public function getTenant(): ?Company
    {
        return $this->tenant;
    }

    /**
     * Check if a tenant is currently resolved.
     */
    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }

    /**
     * Get the branding data to share with React via Inertia.
     * This is what makes each customer's site look unique.
     */
    public function getBrandingData(): array
    {
        if (! $this->tenant) {
            return $this->getDefaultBranding();
        }

        $themeSettings = $this->tenant->theme_settings ?? [];
        $contactInfo   = $this->tenant->contact_info ?? [];
        $socialLinks   = $this->tenant->social_links ?? [];

        return [
            'id'            => $this->tenant->id,
            'name'          => $this->tenant->name,
            'slug'          => $this->tenant->slug,
            'subdomain'     => $this->tenant->subdomain,
            'custom_domain' => $this->tenant->custom_domain,
            'logo_url'      => $this->tenant->logo
                                ? asset('storage/' . $this->tenant->logo)
                                : null,
            'favicon_url'   => $this->tenant->favicon
                                ? asset('storage/' . $this->tenant->favicon)
                                : null,
            'currency'      => $this->tenant->currency ?? 'BDT',
            'timezone'      => $this->tenant->timezone ?? 'Asia/Dhaka',
            'theme'         => [
                'primary_color'   => $themeSettings['primary_color'] ?? '#3B82F6',
                'secondary_color' => $themeSettings['secondary_color'] ?? '#1E40AF',
                'accent_color'    => $themeSettings['accent_color'] ?? '#F59E0B',
                'layout'          => $themeSettings['layout'] ?? 'modern',
                'font'            => $themeSettings['font'] ?? 'inter',
            ],
            'contact'       => [
                'phone'   => $contactInfo['phone'] ?? $this->tenant->phone,
                'email'   => $contactInfo['email'] ?? $this->tenant->email,
                'address' => $contactInfo['address'] ?? $this->tenant->address,
                'website' => $this->tenant->website,
            ],
            'social'        => [
                'facebook'  => $socialLinks['facebook'] ?? null,
                'instagram' => $socialLinks['instagram'] ?? null,
                'twitter'   => $socialLinks['twitter'] ?? null,
                'youtube'   => $socialLinks['youtube'] ?? null,
            ],
        ];
    }

    /**
     * Clear the cache for a specific tenant (call this when company is updated).
     */
    public function clearCache(Company $company): void
    {
        if ($company->custom_domain) {
            Cache::forget('tenant_by_host_' . md5($company->custom_domain));
        }
        if ($company->subdomain) {
            // Clear subdomain-based cache entries
            $appDomain = config('app.domain', 'localhost');
            Cache::forget('tenant_by_host_' . md5($company->subdomain . '.' . $appDomain));
        }
        Cache::forget('tenant_data_' . $company->id);
    }

    /**
     * Extract subdomain from a full host string.
     * e.g. "fashionbd.yourpos.com" → "fashionbd"
     * e.g. "fashionbd" → "fashionbd"
     */
    protected function extractSubdomain(string $host): string
    {
        $host = strtolower(trim($host));

        // Remove www prefix
        $host = preg_replace('/^www\./', '', $host);

        // If it contains dots, take the first segment as subdomain
        if (str_contains($host, '.')) {
            return explode('.', $host)[0];
        }

        return preg_replace('/[^a-z0-9_-]+/', '', $host) ?? $host;
    }

    /**
     * Default branding when no tenant is resolved (e.g. main domain).
     */
    protected function getDefaultBranding(): array
    {
        return [
            'id'            => null,
            'name'          => config('app.name', 'Cloud POS'),
            'slug'          => null,
            'subdomain'     => null,
            'custom_domain' => null,
            'logo_url'      => null,
            'favicon_url'   => null,
            'currency'      => 'BDT',
            'timezone'      => 'Asia/Dhaka',
            'theme'         => [
                'primary_color'   => '#3B82F6',
                'secondary_color' => '#1E40AF',
                'accent_color'    => '#F59E0B',
                'layout'          => 'modern',
                'font'            => 'inter',
            ],
            'contact'       => [
                'phone'   => null,
                'email'   => null,
                'address' => null,
                'website' => null,
            ],
            'social'        => [
                'facebook'  => null,
                'instagram' => null,
                'twitter'   => null,
                'youtube'   => null,
            ],
        ];
    }
}
