<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free Trial',
                'slug' => Str::slug('Free Trial'),
                'price' => 0.00,
                'trial_days' => 14,
                'user_limit' => 2,
                'branch_limit' => 1,
                'features' => json_encode(['POS Access', 'Basic Inventory', 'Daily Sales Report']),
                'status' => 'active',
            ],
            [
                'name' => 'Basic Plan',
                'slug' => Str::slug('Basic Plan'),
                'price' => 999.00, // BDT
                'trial_days' => 0,
                'user_limit' => 5,
                'branch_limit' => 1,
                'features' => json_encode(['All Free Features', 'Barcode Printing', 'Customer Management', 'Monthly Reports']),
                'status' => 'active',
            ],
            [
                'name' => 'Pro Plan',
                'slug' => Str::slug('Pro Plan'),
                'price' => 2499.00, // BDT
                'trial_days' => 0,
                'user_limit' => 15,
                'branch_limit' => 3,
                'features' => json_encode(['All Basic Features', 'Multi-Branch', 'Accounting Integration', 'SMS Notification', 'Priority Support']),
                'status' => 'active',
            ],
            [
                'name' => 'Enterprise',
                'slug' => Str::slug('Enterprise'),
                'price' => 5999.00, // BDT
                'trial_days' => 0,
                'user_limit' => 999, // Unlimited
                'branch_limit' => 999, // Unlimited
                'features' => json_encode(['All Pro Features', 'White-labeling', 'API Access', 'Dedicated Server Support']),
                'status' => 'active',
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('plans')->updateOrInsert(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}