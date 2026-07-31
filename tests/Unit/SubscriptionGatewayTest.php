<?php

namespace Tests\Unit;

use App\Http\Controllers\Company\SubscriptionController;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class SubscriptionGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_callback_marks_transaction_and_subscription_as_successful(): void
    {
        $user = User::create([
            'name' => 'Test Owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('password123'),
        ]);

        $company = Company::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
            'email' => 'test@example.com',
            'user_id' => $user->id,
            'status' => 'inactive',
        ]);

        $plan = Plan::create([
            'name' => 'Pro Plan',
            'slug' => 'pro-plan',
            'price' => 1500.00,
            'trial_days' => 0,
            'user_limit' => 10,
            'branch_limit' => 3,
            'features' => ['Inventory'],
            'status' => 'active',
            'billing_cycle' => 'monthly',
        ]);

        $transaction = Transaction::create([
            'company_id' => $company->id,
            'amount' => 1500.00,
            'currency' => 'BDT',
            'payment_method' => 'sslcommerz',
            'transaction_id' => 'TXN-12345',
            'status' => 'pending',
            'details' => ['billing_cycle' => 'monthly'],
        ]);

        $request = Request::create('/company/subscription/payment/callback', 'GET', [
            'status' => 'success',
            'tran_id' => 'TXN-12345',
            'plan_id' => $plan->id,
        ]);

        $controller = new SubscriptionController();
        $response = $controller->paymentCallback($request);

        $transaction->refresh();
        $subscription = Subscription::where('company_id', $company->id)->latest()->first();

        $this->assertSame('success', $transaction->status);
        $this->assertNotNull($subscription);
        $this->assertSame('active', $subscription->status);
        $this->assertSame($plan->id, $subscription->plan_id);
        $this->assertSame('sslcommerz', $subscription->payment_gateway);
        $this->assertTrue($response instanceof \Symfony\Component\HttpFoundation\RedirectResponse);
    }

    public function test_super_admin_can_store_inactive_lifetime_plan(): void
    {
        $request = Request::create('/super-admin/plans', 'POST', [
            'name' => 'Lifetime Legacy Plan',
            'price' => 2999.00,
            'trial_days' => 7,
            'user_limit' => 20,
            'branch_limit' => 8,
            'billing_cycle' => 'lifetime',
            'status' => 'inactive',
            'features' => "Unlimited access\nPriority support",
        ]);

        $response = (new \App\Http\Controllers\SuperAdmin\PlanController())->store($request);

        $this->assertDatabaseHas('plans', [
            'name' => 'Lifetime Legacy Plan',
            'status' => 'inactive',
            'billing_cycle' => 'lifetime',
        ]);
        $this->assertTrue($response->isRedirect());
    }
}
