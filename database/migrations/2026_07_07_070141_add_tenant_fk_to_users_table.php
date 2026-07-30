<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the deferred FK constraints to the users table.
     *
     * This migration runs AFTER companies and branches tables are created,
     * breaking the circular dependency:
     *   users → companies → users  (circular)
     *   users → branches → companies → users  (circular)
     *
     * The columns (company_id, branch_id) already exist on users as plain
     * unsignedBigInteger columns — we only add the FK constraints here.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->nullOnDelete();

            $table->foreign('branch_id')
                ->references('id')->on('branches')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['branch_id']);
        });
    }
};
