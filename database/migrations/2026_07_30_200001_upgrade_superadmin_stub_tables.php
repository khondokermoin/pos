<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the pivot tables needed for the SuperAdmin module system:
     *   - activity_logs
     *   - business_type_module  (pivot)
     *   - company_module        (pivot)
     *
     * NOTE: global_attributes, global_taxes, global_units are now fully defined
     * in their own create_* migrations. The column-patching logic that was here
     * has been consolidated there and removed from this file.
     */
    public function up(): void
    {
        // ── 1. activity_logs ─────────────────────────────────────────────────
        if (! Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('user_name')->nullable();
                $table->string('user_role')->nullable();
                $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->string('action');
                $table->string('model_type')->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->string('description');
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();

                $table->index(['model_type', 'model_id']);
                $table->index('created_at');
            });
        }

        // ── 2. business_type_module pivot ────────────────────────────────────
        if (! Schema::hasTable('business_type_module')) {
            Schema::create('business_type_module', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_type_id');
                $table->unsignedBigInteger('business_module_id');
                $table->timestamps();

                $table->unique(['business_type_id', 'business_module_id']);
                $table->foreign('business_type_id')->references('id')->on('business_types')->onDelete('cascade');
                $table->foreign('business_module_id')->references('id')->on('business_modules')->onDelete('cascade');
            });
        }

        // ── 3. company_module pivot ──────────────────────────────────────────
        if (! Schema::hasTable('company_module')) {
            Schema::create('company_module', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('business_module_id');
                $table->boolean('is_enabled')->default(true);
                $table->timestamps();

                $table->unique(['company_id', 'business_module_id']);
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->foreign('business_module_id')->references('id')->on('business_modules')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('company_module');
        Schema::dropIfExists('business_type_module');
        Schema::dropIfExists('activity_logs');
    }
};
