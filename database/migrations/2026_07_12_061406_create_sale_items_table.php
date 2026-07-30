<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Consolidated: includes tax_rate and tax_amount snapshot columns (formerly in
     * add_tax_to_sale_items_and_held_orders migration).
     * held_orders table is created in its own migration below.
     */
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('product_name'); // Snapshot for invoice display
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax_rate', 8, 4)->default(0)->comment('Tax rate % at time of sale (snapshot)');
            $table->decimal('tax_amount', 15, 2)->default(0)->comment('Computed tax amount for this line (snapshot)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
