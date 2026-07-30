<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoCompanySeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate([
            'subdomain' => 'demo',
        ], [
            'name' => 'Demo Fashion',
            'email' => 'demo@fashion.example',
            'phone' => null,
            'address' => 'Demo Address',
            'status' => 'active',
        ]);

        $adminEmail = 'admin@demo.com';
        $admin = User::firstOrCreate([
            'email' => $adminEmail,
        ], [
            'name' => 'Demo Company Admin',
            'password' => Hash::make('password'),
            'company_id' => $company->id,
        ]);

        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('Company Admin');
        }
    }
}
