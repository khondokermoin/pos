<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add unit_id, tax_id, reorder_level, attributes columns to product_variants table.
     * These columns are referenced in ProductVariant model but missing from the original migration.
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (! Schema::hasColumn('product_variants', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->after('name')->constrained('units')->nullOnDelete();
            }
            if (! Schema::hasColumn('product_variants', 'tax_id')) {
                $table->foreignId('tax_id')->nullable()->after('unit_id')->constrained('taxes')->nullOnDelete();
            }
            if (! Schema::hasColumn('product_variants', 'reorder_level')) {
                $table->integer('reorder_level')->default(0)->after('selling_price');
            }
            if (! Schema::hasColumn('product_variants', 'attributes')) {
                $table->json('attributes')->nullable()->after('reorder_level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'attributes')) {
                $table->dropColumn('attributes');
            }
            if (Schema::hasColumn('product_variants', 'reorder_level')) {
                $table->dropColumn('reorder_level');
            }
            if (Schema::hasColumn('product_variants', 'tax_id')) {
                $table->dropForeign(['tax_id']);
                $table->dropColumn('tax_id');
            }
            if (Schema::hasColumn('product_variants', 'unit_id')) {
                $table->dropForeign(['unit_id']);
                $table->dropColumn('unit_id');
            }
        });
    }
};
