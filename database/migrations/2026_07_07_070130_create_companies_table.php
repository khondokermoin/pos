<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Consolidated: includes branding fields (favicon, theme_settings, social_links,
     * contact_info) formerly in add_branding_fields_to_companies_table migration.
     *
     * CIRCULAR FK NOTE:
     *   companies.user_id → users.id   (users already exists ✓)
     *   companies.plan_id → plans.id   (plans already exists ✓)
     *   companies.business_type_id → business_types.id  (business_types already exists ✓)
     *
     * users.company_id → companies.id  ← added AFTER this migration in
     *   2026_07_07_070141_add_tenant_fk_to_users_table.php
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // --- Basic Information ---
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Bangladesh');
            $table->string('zip_code')->nullable();

            // --- Branding (consolidated from add_branding_fields migration) ---
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->json('theme_settings')->nullable();
            $table->json('social_links')->nullable();
            $table->json('contact_info')->nullable();

            // --- SaaS & Multi-tenancy ---
            $table->string('subdomain')->unique()->nullable();
            $table->string('custom_domain')->nullable();

            // --- POS & Inventory Core Settings ---
            $table->string('currency', 10)->default('BDT');
            $table->string('timezone', 50)->default('Asia/Dhaka');
            $table->json('settings')->nullable();

            // --- Subscription & Status ---
            $table->enum('status', ['active', 'inactive', 'suspended', 'trial'])->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();

            // --- Admin Owner & Business Type ---
            $table->foreignId('business_type_id')->nullable()->constrained('business_types')->nullOnDelete();
            // user_id: users table already exists at this point (created before companies)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
