<?php

namespace Tests\Unit;

use App\Models\Subscription;
use App\Models\Transaction;
use Tests\TestCase;

class BillingModelsTest extends TestCase
{
    public function test_transaction_model_has_expected_casts(): void
    {
        $casts = (new Transaction())->getCasts();

        $this->assertSame('decimal:2', $casts['amount']);
        $this->assertSame('array', $casts['details']);
    }

    public function test_subscription_model_has_transactions_relationship(): void
    {
        $this->assertTrue(method_exists(Subscription::class, 'transactions'));
    }
}
