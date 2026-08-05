<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Location Columns to Branches Table
 *
 * PURPOSE: Enable the Smart Branch Routing feature for online orders.
 *
 * NEW COLUMNS:
 *   - city            : Human-readable city name (e.g. "Dhaka", "Chittagong")
 *   - district        : Administrative district (e.g. "Dhaka", "Sylhet")
 *   - latitude        : GPS latitude  (decimal degrees, e.g. 23.8103)
 *   - longitude       : GPS longitude (decimal degrees, e.g. 90.4125)
 *   - coverage_areas  : JSON array of city/area names this branch can serve
 *                       e.g. ["Mirpur", "Pallabi", "Kafrul", "Savar"]
 *   - is_default      : If true, this branch is the fallback for unmatched locations
 *   - accepts_online_orders : Whether this branch participates in online ordering
 *
 * ROUTING ALGORITHM (implemented in BranchRoutingService):
 *   1. Exact match: customer city/district matches a branch's coverage_areas
 *   2. Distance match: Haversine formula on lat/lng to find nearest branch
 *   3. Fallback: branch with is_default = true
 *   4. Last resort: first active branch of the company
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // Human-readable location fields
            $table->string('city')->nullable()->after('address')
                ->comment('City where this branch is located, e.g. Dhaka');
            $table->string('district')->nullable()->after('city')
                ->comment('Administrative district, e.g. Dhaka, Chittagong');

            // GPS coordinates for Haversine distance calculation
            $table->decimal('latitude', 10, 7)->nullable()->after('district')
                ->comment('GPS latitude in decimal degrees, e.g. 23.8103');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude')
                ->comment('GPS longitude in decimal degrees, e.g. 90.4125');

            // Coverage area mapping — JSON array of area/city names
            $table->json('coverage_areas')->nullable()->after('longitude')
                ->comment('JSON array of area names this branch serves, e.g. ["Mirpur","Savar"]');

            // Routing control flags
            $table->boolean('is_default')->default(false)->after('coverage_areas')
                ->comment('If true, this branch is the fallback for unmatched customer locations');
            $table->boolean('accepts_online_orders')->default(true)->after('is_default')
                ->comment('Whether this branch participates in online order routing');

            // Index for fast routing queries
            $table->index(['company_id', 'accepts_online_orders', 'is_default'], 'idx_branch_routing');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropIndex('idx_branch_routing');
            $table->dropColumn([
                'city',
                'district',
                'latitude',
                'longitude',
                'coverage_areas',
                'is_default',
                'accepts_online_orders',
            ]);
        });
    }
};
