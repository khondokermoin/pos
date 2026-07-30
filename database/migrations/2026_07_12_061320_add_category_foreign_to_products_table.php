<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add foreign key constraint for category_id on products table.
     * This runs AFTER categories table is created (061318),
     * so the FK reference is safe.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add FK only if categories table exists and FK not already present
            if (Schema::hasTable('categories') && Schema::hasColumn('products', 'category_id')) {
                $table->foreign('category_id')
                      ->references('id')
                      ->on('categories')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'category_id')) {
                try {
                    $table->dropForeign(['category_id']);
                } catch (\Throwable $e) {
                    // ignore if FK doesn't exist
                }
            }
        });
    }
};
