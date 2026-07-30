<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            
            $table->enum('status', ['active', 'cancelled', 'expired', 'trial', 'pending'])->default('pending');
            
            // Billing Cycle (মাসিক নাকি বার্ষিক)
            $table->enum('billing_cycle', ['monthly', 'yearly', 'lifetime'])->default('monthly');
            
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable(); // কখন ক্যান্সেল হয়েছে
            
            // Payment Gateway Details (পেমেন্ট ট্র্যাক করার জন্য)
            $table->string('payment_gateway')->nullable(); // যেমন: sslcommerz, bkash, stripe
            $table->string('transaction_id')->nullable()->unique(); // গেটওয়ে ট্রানজেকশন আইডি
            $table->string('invoice_number')->nullable(); // কাস্টম ইনভয়েস নম্বর
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};