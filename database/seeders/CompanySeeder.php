<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    /**
     * Level 2 — Companies + their Company Admin users.
     *
     * Schema columns used (verified from migrations):
     *   companies : id, name, slug, email, phone, contact_person, address,
     *               city, country, currency, timezone, status, user_id
     *   users     : id, name, email, password, company_id, branch_id
     */
    public function run(): void
    {
        $companies = [
            [
                'company' => [
                    'name'           => 'Alpha Retail Ltd.',
                    'slug'           => 'alpha-retail-ltd',
                    'email'          => 'info@alpharetail.example',
                    'phone'          => '01711000001',
                    'contact_person' => 'Rahim Uddin',
                    'address'        => '12 Motijheel C/A, Dhaka',
                    'city'           => 'Dhaka',
                    'country'        => 'Bangladesh',
                    'subdomain'      => 'alpha-retail',
                    'currency'       => 'BDT',
                    'timezone'       => 'Asia/Dhaka',
                    'status'         => 'active',
                ],
                'admin' => [
                    'name'  => 'Alpha Admin',
                    'email' => 'admin@alpharetail.example',
                ],
            ],
            [
                'company' => [
                    'name'           => 'Beta Electronics Co.',
                    'slug'           => 'beta-electronics-co',
                    'email'          => 'info@betaelectronics.example',
                    'phone'          => '01711000002',
                    'contact_person' => 'Karim Hossain',
                    'address'        => '45 Gulshan Avenue, Dhaka',
                    'city'           => 'Dhaka',
                    'country'        => 'Bangladesh',
                    'subdomain'      => 'beta-electronics',
                    'currency'       => 'BDT',
                    'timezone'       => 'Asia/Dhaka',
                    'status'         => 'active',
                ],
                'admin' => [
                    'name'  => 'Beta Admin',
                    'email' => 'admin@betaelectronics.example',
                ],
            ],
            [
                'company' => [
                    'name'           => 'Gamma Fashion House',
                    'slug'           => 'gamma-fashion-house',
                    'email'          => 'info@gammafashion.example',
                    'phone'          => '01711000003',
                    'contact_person' => 'Nasrin Akter',
                    'address'        => '78 Banani Road, Dhaka',
                    'city'           => 'Dhaka',
                    'country'        => 'Bangladesh',
                    'subdomain'      => 'gamma-fashion',
                    'currency'       => 'BDT',
                    'timezone'       => 'Asia/Dhaka',
                    'status'         => 'active',
                ],
                'admin' => [
                    'name'  => 'Gamma Admin',
                    'email' => 'admin@gammafashion.example',
                ],
            ],
        ];

        foreach ($companies as $data) {
            // ── Create or update the company ──────────────────────────────────
            $company = Company::updateOrCreate(
                ['slug' => $data['company']['slug']],
                $data['company']
            );

            // ── Create or update the Company Admin user ───────────────────────
            // branch_id is NULL for Company Admins — they operate at company level
            $admin = User::updateOrCreate(
                ['email' => $data['admin']['email']],
                [
                    'name'       => $data['admin']['name'],
                    'password'   => Hash::make('password'),
                    'company_id' => $company->id,
                    'branch_id'  => null,
                ]
            );

            // Assign Spatie role
            $admin->syncRoles(['Company Admin']);

            // Link the admin as the company owner (companies.user_id)
            $company->user_id = $admin->id;
            $company->save();

            $this->command->info(
                "  ✅ Company: [{$company->name}]  Admin: [{$admin->email}]"
            );
        }

        $this->command->info('✅ CompanySeeder completed — 3 companies seeded.');
    }
}
