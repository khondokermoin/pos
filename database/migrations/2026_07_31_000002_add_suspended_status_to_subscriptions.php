<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subscriptions')) {
            $driver = Schema::getConnection()->getDriverName();

            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status ENUM('active', 'cancelled', 'expired', 'trial', 'pending', 'suspended') NOT NULL DEFAULT 'pending'");
                return;
            }

            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF');

                // IMPORTANT: build the replacement under a NEW name and rename it
                // into place at the end — do NOT rename the original "subscriptions"
                // table away. SQLite auto-rewrites other tables' FK clauses that
                // reference a table when it is renamed (e.g. transactions.subscription_id
                // would silently start pointing at "subscriptions_old"), and once that
                // table is dropped those FKs are left dangling. Dropping the original
                // by its real name does NOT trigger that rewrite, so this order keeps
                // every other table's FK intact.
                Schema::create('subscriptions_new', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('company_id')->constrained()->onDelete('cascade');
                    $table->foreignId('plan_id')->constrained()->onDelete('cascade');
                    $table->enum('status', ['active', 'cancelled', 'expired', 'trial', 'pending', 'suspended'])->default('pending');
                    $table->enum('billing_cycle', ['monthly', 'yearly', 'lifetime'])->default('monthly');
                    $table->timestamp('started_at')->nullable();
                    $table->timestamp('ends_at')->nullable();
                    $table->timestamp('trial_ends_at')->nullable();
                    $table->timestamp('cancelled_at')->nullable();
                    $table->string('payment_gateway')->nullable();
                    $table->string('transaction_id')->nullable()->unique();
                    $table->string('invoice_number')->nullable();
                    $table->timestamps();
                });

                DB::statement('INSERT INTO subscriptions_new (id, company_id, plan_id, status, billing_cycle, started_at, ends_at, trial_ends_at, cancelled_at, payment_gateway, transaction_id, invoice_number, created_at, updated_at)
                    SELECT id, company_id, plan_id, status, billing_cycle, started_at, ends_at, trial_ends_at, cancelled_at, payment_gateway, transaction_id, invoice_number, created_at, updated_at
                    FROM subscriptions');

                Schema::drop('subscriptions');
                Schema::rename('subscriptions_new', 'subscriptions');

                DB::statement('PRAGMA foreign_keys = ON');
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('subscriptions')) {
            $driver = Schema::getConnection()->getDriverName();

            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status ENUM('active', 'cancelled', 'expired', 'trial', 'pending') NOT NULL DEFAULT 'pending'");
            }
        }
    }
};
