<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Level 1: The one and only Super Admin ────────────────────────────
        // company_id and branch_id are intentionally NULL — Super Admin is
        // above all tenants and belongs to no company or branch.
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@system.com'],
            [
                'name'       => 'Super Admin',
                'password'   => Hash::make('password'),
                'company_id' => null,
                'branch_id'  => null,
            ]
        );

        // Assign Spatie role (idempotent — won't duplicate if already assigned)
        $superAdmin->syncRoles(['Super Admin']);

        $this->command->info('✅ Super Admin seeded → superadmin@system.com / password');
    }
}
