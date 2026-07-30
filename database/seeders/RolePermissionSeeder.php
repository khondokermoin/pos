<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Reset Spatie permission cache ───────────────────────────────────
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── 1. Define all permissions ────────────────────────────────────────
        $permissions = [
            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',
            // Roles
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            // Companies
            'view companies',
            'create companies',
            'edit companies',
            'delete companies',
            // Plans & Subscriptions
            'view plans',
            'create plans',
            'edit plans',
            'delete plans',
            'view subscriptions',
            'manage subscriptions',
            'view transactions',
            // Products & Categories
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            // Sales
            'view sales',
            'create sales',
            'edit sales',
            'delete sales',
            // Purchases
            'view purchases',
            'create purchases',
            'edit purchases',
            'delete purchases',
            // Customers & Suppliers
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            // Reports & Settings
            'view reports',
            'view settings',
            'manage settings',
            'manage attributes',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => 'web',
            ]);
        }

        // ─── 2. Super Admin — all permissions ────────────────────────────────
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // ─── 3. Company Admin ─────────────────────────────────────────────────
        $companyAdmin = Role::firstOrCreate(['name' => 'Company Admin', 'guard_name' => 'web']);
        $companyAdmin->syncPermissions([
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            'view sales',
            'create sales',
            'edit sales',
            'delete sales',
            'view purchases',
            'create purchases',
            'edit purchases',
            'delete purchases',
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            'view reports',
            'view settings',
            'manage settings',
            'manage attributes',
        ]);

        // ─── 4. Manager ───────────────────────────────────────────────────────
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'view users',
            'view products',
            'create products',
            'edit products',
            'view categories',
            'view sales',
            'create sales',
            'edit sales',
            'view purchases',
            'create purchases',
            'view customers',
            'create customers',
            'view suppliers',
            'view reports',
            'view settings',
        ]);

        // ─── 5. Salesman ──────────────────────────────────────────────────────
        $salesman = Role::firstOrCreate(['name' => 'Salesman', 'guard_name' => 'web']);
        $salesman->syncPermissions([
            'view products',
            'create sales',
            'view sales',
            'view customers',
            'create customers',
        ]);

        $this->command->info('✅ Roles & Permissions seeded successfully.');
    }
}
