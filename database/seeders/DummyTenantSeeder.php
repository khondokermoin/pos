<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DummyTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = FakerFactory::create();

        $companyAdminRole = Role::firstOrCreate([
            'name' => 'Company Admin',
            'guard_name' => 'web',
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $companyName = $faker->company();
            $companySlug = Str::slug($companyName) . '-' . $i;
            $companyEmail = $companySlug . '@example.com';
            $subdomain = $companySlug;

            $company = Company::firstOrCreate(
                ['email' => $companyEmail],
                [
                    'name' => $companyName,
                    'slug' => $companySlug,
                    'email' => $companyEmail,
                    'phone' => $faker->phoneNumber(),
                    'contact_person' => $faker->name(),
                    'website' => $faker->domainName(),
                    'address' => $faker->address(),
                    'city' => $faker->city(),
                    'country' => 'Bangladesh',
                    'zip_code' => $faker->postcode(),
                    'subdomain' => $subdomain,
                    'custom_domain' => $faker->optional()->domainName(),
                    'currency' => 'BDT',
                    'timezone' => 'Asia/Dhaka',
                    'status' => 'active',
                ]
            );

            $company->forceFill([
                'name' => $companyName,
                'slug' => $companySlug,
                'email' => $companyEmail,
                'phone' => $faker->phoneNumber(),
                'contact_person' => $faker->name(),
                'website' => $faker->domainName(),
                'address' => $faker->address(),
                'city' => $faker->city(),
                'country' => 'Bangladesh',
                'zip_code' => $faker->postcode(),
                'subdomain' => $subdomain,
                'custom_domain' => $faker->optional()->domainName(),
                'currency' => 'BDT',
                'timezone' => 'Asia/Dhaka',
                'status' => 'active',
            ])->save();

            $branchCount = $faker->numberBetween(1, 3);
            $branches = [];

            for ($branchIndex = 1; $branchIndex <= $branchCount; $branchIndex++) {
                $branch = $company->branches()->create([
                    'name' => $companyName . ' Branch ' . $branchIndex,
                    'email' => $faker->safeEmail(),
                    'phone' => $faker->phoneNumber(),
                    'address' => $faker->address(),
                    'status' => 'active',
                ]);

                $branches[] = $branch;
            }

            $firstBranch = $branches[0] ?? $company->branches()->create([
                'name' => $companyName . ' Branch 1',
                'email' => $faker->safeEmail(),
                'phone' => $faker->phoneNumber(),
                'address' => $faker->address(),
                'status' => 'active',
            ]);

            $adminName = $faker->name() . ' (' . $companyName . ')';
            $adminEmail = Str::slug($companyName) . '-admin-' . $i . '@example.com';

            $admin = User::firstOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => $adminName,
                    'password' => Hash::make('password'),
                    'company_id' => $company->id,
                    'branch_id' => $firstBranch->id,
                ]
            );

            $admin->forceFill([
                'name' => $adminName,
                'password' => Hash::make('password'),
                'company_id' => $company->id,
                'branch_id' => $firstBranch->id,
            ])->save();

            if (method_exists($admin, 'assignRole')) {
                $admin->assignRole($companyAdminRole);
            }

            $company->forceFill(['user_id' => $admin->id])->save();
        }
    }
}
