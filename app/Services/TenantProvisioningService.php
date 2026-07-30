<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\BusinessModule;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\GlobalAttribute;
use App\Models\GlobalCategory;
use App\Models\GlobalTax;
use App\Models\GlobalUnit;
use App\Models\Tax;
use App\Models\Unit;

class TenantProvisioningService
{
    public function provision(Company $company): void
    {
        $this->createDefaultUnits($company);
        $this->createDefaultTaxes($company);
        $this->createDefaultCategories($company);
        $this->createDefaultCustomer($company);
        $this->createDefaultAttributes($company);
        $this->assignDefaultModules($company);

        // Log provisioning
        ActivityLog::create([
            'user_id'     => null,
            'user_name'   => 'System',
            'user_role'   => 'System',
            'company_id'  => $company->id,
            'action'      => 'created',
            'model_type'  => Company::class,
            'model_id'    => $company->id,
            'description' => "New tenant provisioned: {$company->name}",
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);

        // Send welcome email using the stored EmailTemplate (slug: welcome-tenant)
        $this->sendWelcomeEmail($company);
    }

    protected function sendWelcomeEmail(Company $company): void
    {
        // Only send if the company owner has an email address
        $ownerEmail = $company->email ?? null;
        if (! $ownerEmail) {
            return;
        }

        try {
            app(EmailTemplateService::class)->send(
                'welcome-tenant',
                $ownerEmail,
                [
                    'company_name'   => $company->name,
                    'owner_name'     => $company->owner_name ?? $company->name,
                    'login_url'      => config('app.url') . '/login',
                    'support_email'  => config('mail.from.address', 'support@example.com'),
                    'app_name'       => config('app.name', 'Cloud POS'),
                ],
                $company->owner_name ?? $company->name
            );
        } catch (\Throwable $e) {
            // Never let email failure break tenant provisioning
            \Illuminate\Support\Facades\Log::warning(
                "TenantProvisioningService: Welcome email failed for {$company->name}: " . $e->getMessage()
            );
        }
    }

    /**
     * Create units from GlobalUnit master data (if available), else use hardcoded defaults.
     */
    protected function createDefaultUnits(Company $company): void
    {
        $globalUnits = GlobalUnit::active()->get();

        if ($globalUnits->isNotEmpty()) {
            foreach ($globalUnits as $globalUnit) {
                Unit::firstOrCreate(
                    ['company_id' => $company->id, 'name' => $globalUnit->name],
                    [
                        'short_code' => $globalUnit->short_code,
                        'is_active'  => true,
                    ]
                );
            }
        } else {
            // Fallback hardcoded defaults
            $units = [
                ['name' => 'Pcs',  'short_code' => 'Pcs'],
                ['name' => 'Kg',   'short_code' => 'Kg'],
                ['name' => 'Ltr',  'short_code' => 'Ltr'],
                ['name' => 'Box',  'short_code' => 'Box'],
            ];

            foreach ($units as $unit) {
                Unit::firstOrCreate(
                    ['company_id' => $company->id, 'name' => $unit['name']],
                    ['short_code' => $unit['short_code'], 'is_active' => true]
                );
            }
        }
    }

    /**
     * Create taxes from GlobalTax master data (if available), else use hardcoded defaults.
     */
    protected function createDefaultTaxes(Company $company): void
    {
        $globalTaxes = GlobalTax::active()->get();

        if ($globalTaxes->isNotEmpty()) {
            foreach ($globalTaxes as $globalTax) {
                Tax::firstOrCreate(
                    ['company_id' => $company->id, 'name' => $globalTax->name],
                    [
                        'rate'      => $globalTax->rate,
                        'is_active' => true,
                    ]
                );
            }
        } else {
            // Fallback hardcoded defaults
            $taxes = [
                ['name' => 'No VAT',       'rate' => 0],
                ['name' => 'Standard VAT', 'rate' => 5],
            ];

            foreach ($taxes as $tax) {
                Tax::firstOrCreate(
                    ['company_id' => $company->id, 'name' => $tax['name']],
                    ['rate' => $tax['rate'], 'is_active' => true]
                );
            }
        }
    }

    /**
     * Create categories from GlobalCategory master data (if available).
     */
    protected function createDefaultCategories(Company $company): void
    {
        $globalCategories = GlobalCategory::active()->get();

        if ($globalCategories->isNotEmpty()) {
            foreach ($globalCategories as $globalCategory) {
                Category::firstOrCreate(
                    ['company_id' => $company->id, 'name' => $globalCategory->name],
                    ['is_active' => true]
                );
            }
        }
        // If no global categories, we don't create any — company can add their own
    }

    /**
     * Create the default walk-in customer.
     */
    protected function createDefaultCustomer(Company $company): void
    {
        Customer::firstOrCreate(
            ['company_id' => $company->id, 'is_walk_in' => true],
            [
                'name'  => 'Walk-in Customer',
                'phone' => 'N/A',
                'email' => null,
            ]
        );
    }

    /**
     * Create attributes from GlobalAttribute master data (if available),
     * else fall back to business-type-specific defaults.
     */
    protected function createDefaultAttributes(Company $company): void
    {
        $globalAttributes = GlobalAttribute::active()->get();

        if ($globalAttributes->isNotEmpty()) {
            foreach ($globalAttributes as $globalAttr) {
                $attribute = Attribute::firstOrCreate(
                    ['company_id' => $company->id, 'name' => $globalAttr->name]
                );

                foreach ($globalAttr->values ?? [] as $value) {
                    AttributeValue::firstOrCreate([
                        'attribute_id' => $attribute->id,
                        'value'        => $value,
                    ]);
                }
            }
            return;
        }

        // Fallback: business-type-specific defaults
        $businessTypeName      = $company->businessType->name ?? '';
        $normalizedBusinessType = trim(strtolower($businessTypeName));
        $defaultAttributes     = [];

        if (str_contains($normalizedBusinessType, 'fashion')) {
            $defaultAttributes = [
                ['name' => 'Size',  'values' => ['S', 'M', 'L', 'XL']],
                ['name' => 'Color', 'values' => ['Black', 'White', 'Red']],
            ];
        } elseif (str_contains($normalizedBusinessType, 'electronics')) {
            $defaultAttributes = [
                ['name' => 'RAM',     'values' => ['4GB', '8GB', '16GB']],
                ['name' => 'Storage', 'values' => ['64GB', '128GB', '256GB']],
            ];
        } elseif (str_contains($normalizedBusinessType, 'restaurant')) {
            $defaultAttributes = [
                ['name' => 'Portion', 'values' => ['Regular', 'Large', 'Family']],
            ];
        } elseif (
            str_contains($normalizedBusinessType, 'retail') ||
            str_contains($normalizedBusinessType, 'grocery') ||
            str_contains($normalizedBusinessType, 'general')
        ) {
            $defaultAttributes = [
                ['name' => 'Weight/Volume', 'values' => ['100g', '250g', '500g', '1Kg', '5Kg']],
                ['name' => 'Pack Size',     'values' => ['Single', 'Half Dozen', 'Dozen', 'Family Pack']],
            ];
        }

        foreach ($defaultAttributes as $attributeData) {
            $attribute = Attribute::firstOrCreate([
                'company_id' => $company->id,
                'name'       => $attributeData['name'],
            ]);

            foreach ($attributeData['values'] as $value) {
                AttributeValue::firstOrCreate([
                    'attribute_id' => $attribute->id,
                    'value'        => $value,
                ]);
            }
        }
    }

    /**
     * Assign default modules to the new company.
     * Core modules are always assigned. Business-type-specific modules are also assigned.
     */
    protected function assignDefaultModules(Company $company): void
    {
        // Get all core modules
        $coreModules = BusinessModule::where('is_core', true)->where('is_active', true)->get();

        $syncData = [];
        foreach ($coreModules as $module) {
            $syncData[$module->id] = ['is_enabled' => true];
        }

        // Get modules assigned to this company's business type
        if ($company->business_type_id) {
            $businessTypeModules = BusinessModule::whereHas('businessTypes', function ($q) use ($company) {
                $q->where('business_types.id', $company->business_type_id);
            })->where('is_active', true)->get();

            foreach ($businessTypeModules as $module) {
                $syncData[$module->id] = ['is_enabled' => true];
            }
        }

        if (!empty($syncData)) {
            $company->modules()->sync($syncData);
        }
    }
}
