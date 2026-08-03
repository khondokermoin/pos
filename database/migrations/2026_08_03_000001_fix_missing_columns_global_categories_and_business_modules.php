<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add missing columns to global_categories and business_modules tables.
     *
     * Both tables were created as stubs (id + timestamps only).
     * The column-patching that was supposed to be in the original migrations
     * was never added, causing TenantProvisioningService to fail with
     * "Unknown column 'is_active'" when creating a new company.
     */
    public function up(): void
    {
        // ── 1. global_categories ─────────────────────────────────────────────
        Schema::table('global_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('global_categories', 'name')) {
                $table->string('name')->after('id');
            }
            if (! Schema::hasColumn('global_categories', 'slug')) {
                $table->string('slug')->unique()->nullable()->after('name');
            }
            if (! Schema::hasColumn('global_categories', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }
            if (! Schema::hasColumn('global_categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
        });

        // ── 2. business_modules ──────────────────────────────────────────────
        Schema::table('business_modules', function (Blueprint $table) {
            if (! Schema::hasColumn('business_modules', 'name')) {
                $table->string('name')->after('id');
            }
            if (! Schema::hasColumn('business_modules', 'slug')) {
                $table->string('slug')->unique()->nullable()->after('name');
            }
            if (! Schema::hasColumn('business_modules', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }
            if (! Schema::hasColumn('business_modules', 'icon')) {
                $table->string('icon')->nullable()->after('description');
            }
            if (! Schema::hasColumn('business_modules', 'is_core')) {
                $table->boolean('is_core')->default(false)->after('icon');
            }
            if (! Schema::hasColumn('business_modules', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_core');
            }
        });
    }

    public function down(): void
    {
        Schema::table('global_categories', function (Blueprint $table) {
            $table->dropColumn(['name', 'slug', 'description', 'is_active']);
        });

        Schema::table('business_modules', function (Blueprint $table) {
            $table->dropColumn(['name', 'slug', 'description', 'icon', 'is_core', 'is_active']);
        });
    }
};
