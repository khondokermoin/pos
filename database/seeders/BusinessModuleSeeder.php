<?php

namespace Database\Seeders;

use App\Models\BusinessModule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BusinessModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            // Core modules — always enabled for every company
            [
                'name'        => 'POS Terminal',
                'slug'        => 'pos-terminal',
                'description' => 'Point of Sale terminal for processing sales',
                'icon'        => 'ti ti-device-desktop',
                'is_core'     => true,
                'is_active'   => true,
            ],
            [
                'name'        => 'Inventory Management',
                'slug'        => 'inventory-management',
                'description' => 'Product catalog, stock tracking and adjustments',
                'icon'        => 'ti ti-package',
                'is_core'     => true,
                'is_active'   => true,
            ],
            [
                'name'        => 'Customer Management',
                'slug'        => 'customer-management',
                'description' => 'Customer records and purchase history',
                'icon'        => 'ti ti-users',
                'is_core'     => true,
                'is_active'   => true,
            ],
            [
                'name'        => 'Supplier & Purchasing',
                'slug'        => 'supplier-purchasing',
                'description' => 'Supplier management and purchase orders',
                'icon'        => 'ti ti-truck',
                'is_core'     => true,
                'is_active'   => true,
            ],
            [
                'name'        => 'Branch Management',
                'slug'        => 'branch-management',
                'description' => 'Multi-branch setup and management',
                'icon'        => 'ti ti-building-store',
                'is_core'     => true,
                'is_active'   => true,
            ],
            [
                'name'        => 'Reports & Analytics',
                'slug'        => 'reports-analytics',
                'description' => 'Sales reports, stock reports and analytics',
                'icon'        => 'ti ti-chart-bar',
                'is_core'     => true,
                'is_active'   => true,
            ],

            // Optional modules — can be enabled per company/business type
            [
                'name'        => 'Payroll & HR',
                'slug'        => 'payroll-hr',
                'description' => 'Employee management, payroll and salary processing',
                'icon'        => 'ti ti-users-group',
                'is_core'     => false,
                'is_active'   => true,
            ],
            [
                'name'        => 'Cash Book & Accounting',
                'slug'        => 'cash-book-accounting',
                'description' => 'Cash accounts, transfers and financial tracking',
                'icon'        => 'ti ti-cash',
                'is_core'     => false,
                'is_active'   => true,
            ],
            [
                'name'        => 'Loan Management',
                'slug'        => 'loan-management',
                'description' => 'Loan authorities, loans and payment tracking',
                'icon'        => 'ti ti-credit-card',
                'is_core'     => false,
                'is_active'   => true,
            ],
            [
                'name'        => 'Asset Management',
                'slug'        => 'asset-management',
                'description' => 'Company asset tracking and depreciation',
                'icon'        => 'ti ti-building',
                'is_core'     => false,
                'is_active'   => true,
            ],
            [
                'name'        => 'Quotations',
                'slug'        => 'quotations',
                'description' => 'Create and manage sales quotations',
                'icon'        => 'ti ti-file-invoice',
                'is_core'     => false,
                'is_active'   => true,
            ],
            [
                'name'        => 'Sales Returns',
                'slug'        => 'sales-returns',
                'description' => 'Process and track sales returns',
                'icon'        => 'ti ti-arrow-back-up',
                'is_core'     => false,
                'is_active'   => true,
            ],
            [
                'name'        => 'Purchase Returns',
                'slug'        => 'purchase-returns',
                'description' => 'Process and track purchase returns',
                'icon'        => 'ti ti-arrow-forward-up',
                'is_core'     => false,
                'is_active'   => true,
            ],
            [
                'name'        => 'Barcode Printing',
                'slug'        => 'barcode-printing',
                'description' => 'Generate and print product barcodes',
                'icon'        => 'ti ti-barcode',
                'is_core'     => false,
                'is_active'   => true,
            ],
            [
                'name'        => 'Shift Management',
                'slug'        => 'shift-management',
                'description' => 'Open/close shifts and track daily sales',
                'icon'        => 'ti ti-clock',
                'is_core'     => false,
                'is_active'   => true,
            ],
        ];

        foreach ($modules as $module) {
            BusinessModule::updateOrCreate(
                ['slug' => $module['slug']],
                $module
            );
        }

        $this->command->info('✅ Business modules seeded: ' . count($modules) . ' modules created/updated.');
    }
}
