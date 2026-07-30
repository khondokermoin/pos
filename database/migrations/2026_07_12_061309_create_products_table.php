<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * NOTE: category_id uses nullable() + no constrained() here because
     * categories table is created AFTER this migration (061318 > 061309).
     * The FK constraint is added later via a separate migration to avoid
     * "Table doesn't exist" error.
     *
     * Consolidated: is_bulk column is defined here (formerly patched by
     * add_is_bulk_to_products_table migration — now deleted).
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            // category_id stored as plain unsignedBigInteger — FK added after categories table exists
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('has_variants')->default(false);
            $table->boolean('is_bulk')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
