<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Branch;
use App\Models\User;
use App\Models\BusinessType; // <-- এটি যোগ করুন
use Illuminate\Support\Facades\Hash;

class TenantBaseSetupSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info(' Setting up Base Tenant Data...');

        // ১. একটি ডিফল্ট Business Type তৈরি করি (যদি না থাকে)
        $businessType = BusinessType::firstOrCreate(
            ['name' => 'Retail / Super Shop'],
            ['slug' => 'retail', 'is_active' => true]
        );
        $this->command->info(' Business Type Found/Created: ' . $businessType->name);

        // ২. প্রথমে একটি ডিফল্ট ইউজার তৈরি করি (Company Admin/Manager)
        $user = User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $this->command->info(' User Created/Found: ' . $user->name . ' (ID: ' . $user->id . ')');

        // ৩. এখন ওই ইউজারকে Owner হিসেবে ধরে একটি ডিফল্ট কোম্পানি তৈরি করি
        $company = Company::firstOrCreate(
            ['name' => 'Demo Retail Ltd.'],
            [
                'email' => 'info@demoretail.com',
                'phone' => '01711111111',
                'address' => 'Dhaka, Bangladesh',
                'status' => 'active',
                'user_id' => $user->id,
                'business_type_id' => $businessType->id, // <-- এখানে business_type_id পাস করা হচ্ছে
                'plan_id' => 1, // PlanSeeder থেকে আসা প্রথম প্ল্যান
            ]
        );
        $this->command->info(' Company Created/Found: ' . $company->name . ' (ID: ' . $company->id . ')');

        // ৪. ওই কোম্পানির অধীনে একটি ডিফল্ট ব্রাঞ্চ তৈরি করি
        $branch = Branch::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Main Branch'],
            [
                'email' => 'mainbranch@demoretail.com',
                'phone' => '01722222222',
                'address' => 'Gulshan, Dhaka',
            ]
        );
        $this->command->info(' Branch Created/Found: ' . $branch->name . ' (ID: ' . $branch->id . ')');

        // ৫. ইউজারের প্রোফাইলে company_id এবং branch_id আপডেট করে দিই
        if (!$user->company_id || !$user->branch_id) {
            $user->company_id = $company->id;
            $user->branch_id = $branch->id;
            $user->save();
            $this->command->info(' User mapped to Company ID: ' . $user->company_id . ' & Branch ID: ' . $user->branch_id);
        }

        $this->command->info('Base Tenant Setup Completed Successfully!');
        $this->command->info('You can now login with:');
        $this->command->info('   Email: admin@demo.com');
        $this->command->info('   Password: password');
    }
}
