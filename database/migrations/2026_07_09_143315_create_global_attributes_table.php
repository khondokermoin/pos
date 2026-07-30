<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Consolidated: includes name, values, is_active columns (formerly added by
     * upgrade_superadmin_stub_tables migration).
     */
    public function up(): void
    {
        Schema::create('global_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('values')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_attributes');
    }
};
