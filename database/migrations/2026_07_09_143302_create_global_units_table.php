<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Consolidated: global_units is platform-wide (no company_id — removed by
     * upgrade_superadmin_stub_tables migration). name and short_code are NOT NULL.
     */
    public function up(): void
    {
        Schema::create('global_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_code', 20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_units');
    }
};
