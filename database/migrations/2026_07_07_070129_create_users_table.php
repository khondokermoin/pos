<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates users, password_reset_tokens, and sessions tables.
     *
     * IMPORTANT — circular FK note:
     *   users.company_id  → companies.id
     *   users.branch_id   → branches.id
     *   companies.user_id → users.id        ← companies is created AFTER users
     *   branches.company_id → companies.id  ← branches is created AFTER companies
     *
     * To break the circular dependency we create users WITHOUT the company_id /
     * branch_id foreign-key constraints here.  The FK constraints are added in
     * 2026_07_07_070141_add_tenant_fk_to_users_table.php which runs after both
     * companies and branches tables exist.
     */
    public function up(): void
    {
        // 1. Users table — tenant FK columns stored as plain unsignedBigInteger;
        //    FK constraints are added after companies + branches tables exist.
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // Plain columns — FK constraints added in the post-companies migration
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Password Reset Tokens table
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. Sessions table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
