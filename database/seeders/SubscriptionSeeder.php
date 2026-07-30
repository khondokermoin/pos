<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Str;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ডাটাবেস থেকে সব প্ল্যান এবং কোম্পানি নিয়ে আসা
        $plans = Plan::all();
        $companies = Company::all();

        // যদি প্ল্যান বা কোম্পানি না থাকে, তাহলে সাবস্ক্রিপশন তৈরি করা যাবে না
        if ($plans->isEmpty() || $companies->isEmpty()) {
            $this->command->warn('⚠️ No Plans or Companies found. Please run PlanSeeder and CompanySeeder first!');
            return;
        }

        $statuses = ['active', 'cancelled', 'expired', 'trial', 'pending'];
        $billingCycles = ['monthly', 'yearly', 'lifetime'];
        $gateways = ['sslcommerz', 'bkash', 'nagad', 'stripe', null]; // null মানে পেমেন্ট হয়নি (যেমন: ফ্রি ট্রায়াল)

        foreach ($companies as $company) {
            // প্রতিটি কোম্পানির জন্য ১ থেকে ৩টি সাবস্ক্রিপশন হিস্টরি তৈরি করা হলো
            $count = rand(1, 3);

            for ($i = 0; $i < $count; $i++) {
                $plan = $plans->random();
                $status = $statuses[array_rand($statuses)];
                $billingCycle = $billingCycles[array_rand($billingCycles)];
                $gateway = $gateways[array_rand($gateways)];

                // তারিখ ও সময় হিসাব করা (লজিক্যাল ডাটা তৈরির জন্য)
                $startedAt = now()->subDays(rand(30, 365)); // গত ১ বছরের মধ্যে যেকোনো দিন শুরু হয়েছে
                $trialEndsAt = ($status === 'trial') ? $startedAt->copy()->addDays($plan->trial_days) : null;
                
                $endsAt = null;
                if ($billingCycle === 'monthly') {
                    $endsAt = $startedAt->copy()->addMonth();
                } elseif ($billingCycle === 'yearly') {
                    $endsAt = $startedAt->copy()->addYear();
                } elseif ($billingCycle === 'lifetime') {
                    $endsAt = $startedAt->copy()->addYears(99);
                }

                $cancelledAt = ($status === 'cancelled') ? now()->subDays(rand(1, 15)) : null;

                // ইউনিক ট্রানজেকশন আইডি তৈরি (যাতে ডুপ্লিকেট না হয়)
                $transactionId = $gateway ? 'TXN_' . strtoupper(Str::random(8)) . '_' . time() . '_' . $company->id . $i : null;

                Subscription::create([
                    'company_id'      => $company->id,
                    'plan_id'         => $plan->id,
                    'status'          => $status,
                    'billing_cycle'   => $billingCycle,
                    'started_at'      => $startedAt,
                    'ends_at'         => $endsAt,
                    'trial_ends_at'   => $trialEndsAt,
                    'cancelled_at'    => $cancelledAt,
                    'payment_gateway' => $gateway,
                    'transaction_id'  => $transactionId,
                    'invoice_number'  => $gateway ? 'INV-' . rand(100000, 999999) : null,
                ]);
            }
        }
        
        $this->command->info('✅ Subscriptions seeded successfully!');
    }
}