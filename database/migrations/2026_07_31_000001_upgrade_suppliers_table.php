<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            if (! Schema::hasColumn('suppliers', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('address');
            }
            if (! Schema::hasColumn('suppliers', 'notes')) {
                $table->text('notes')->nullable()->after('contact_person');
            }
            if (! Schema::hasColumn('suppliers', 'status')) {
                $table->string('status')->default('active')->after('notes');
            }
        });

        // Migrate existing is_active boolean data → status string
        if (Schema::hasColumn('suppliers', 'is_active')) {
            DB::statement("UPDATE `suppliers` SET `status` = CASE WHEN `is_active` = 1 THEN 'active' ELSE 'inactive' END");

            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            if (! Schema::hasColumn('suppliers', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('address');
            }
        });

        if (Schema::hasColumn('suppliers', 'status')) {
            DB::statement("UPDATE `suppliers` SET `is_active` = CASE WHEN `status` = 'active' THEN 1 ELSE 0 END");

            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropColumn(['status', 'contact_person', 'notes']);
            });
        }
    }
};
