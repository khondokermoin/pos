<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Consolidated: includes 'status' and 'manager_id' columns (formerly in
     * add_status_to_branches_table and add_manager_id_to_branches_table migrations).
     *
     * FK ORDER:
     *   branches.company_id → companies.id  (companies created before branches ✓)
     *   branches.manager_id → users.id      (users created before branches ✓)
     *
     * users.branch_id → branches.id  ← added AFTER this migration in
     *   2026_07_07_070141_add_tenant_fk_to_users_table.php
     */
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            // Which company this branch belongs to
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();

            $table->string('name');
            $table->string('status')->default('active');
            $table->string('email')->nullable();
            // Branch manager (nullable FK to users — set null if user deleted)
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
