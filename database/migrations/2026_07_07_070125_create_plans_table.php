<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Consolidated: includes 'billing_cycle' column (formerly in
     * add_billing_cycle_to_plans_table migration).
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);

            $table->integer('trial_days')->default(14);
            $table->integer('user_limit')->default(5);
            $table->integer('branch_limit')->default(1);

            $table->json('features')->nullable();

            $table->enum('billing_cycle', ['monthly', 'yearly', 'lifetime'])
                ->default('monthly');

            $table->enum('status', ['active', 'inactive', 'draft'])
                ->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
