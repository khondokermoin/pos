<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoBranchSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['subdomain' => 'demo'],
            [
                'name' => 'Demo Fashion',
                'email' => 'demo@fashion.example',
                'phone' => null,
                'address' => 'Demo Address',
                'status' => 'active',
            ]
        );

        $managerRole = Role::firstOrCreate([
            'name' => 'Manager',
            'guard_name' => 'web',
        ]);

        $branch = Branch::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Kunze-Wolf Branch 2'],
            [
                'email' => 'kunze-wolf-branch2@example.com',
                'phone' => '01700000002',
                'address' => 'Kunze-Wolf Road, Dhaka',
                'status' => 'active',
            ]
        );

        $manager = User::firstOrCreate(
            ['email' => 'kunze-wolf-manager@example.com'],
            [
                'name' => 'Kunze-Wolf Branch 2 Manager',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
                'branch_id' => $branch->id,
            ]
        );

        $manager->forceFill([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
        ])->save();

        if (method_exists($manager, 'assignRole')) {
            $manager->assignRole($managerRole);
        }

        if ($branch->manager_id !== $manager->id) {
            $branch->manager_id = $manager->id;
            $branch->save();
        }

        $this->command->info('Demo branch created: ' . $branch->name . ' with Manager: ' . $manager->email);
    }
}
