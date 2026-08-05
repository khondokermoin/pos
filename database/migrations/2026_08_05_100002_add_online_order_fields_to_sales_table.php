<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Online Order Fields to Sales Table
 *
 * PURPOSE: Distinguish online orders from POS sales and capture delivery data.
 *
 * NEW COLUMNS:
 *   - order_type          : 'pos' (default) or 'online'
 *   - delivery_city       : Customer's city as entered at checkout
 *   - delivery_district   : Customer's district as entered at checkout
 *   - delivery_area       : Customer's specific area/neighbourhood
 *   - delivery_address    : Full delivery address string
 *   - customer_lat        : Customer GPS latitude (if browser geolocation was granted)
 *   - customer_lng        : Customer GPS longitude (if browser geolocation was granted)
 *   - routed_branch_id    : The branch that was auto-assigned by BranchRoutingService
 *   - routing_method      : How the branch was selected: 'coverage_match', 'distance', 'default', 'fallback'
 *   - routing_distance_km : Calculated distance in km between customer and assigned branch
 *   - customer_name       : Guest customer name (for online orders without a customer account)
 *   - customer_phone      : Guest customer phone
 *   - customer_email      : Guest customer email
 *   - notes               : Order notes from customer
 *
 * REPORTING QUERIES (used in Company Admin Online Orders Report):
 *   - WHERE order_type = 'online'
 *   - GROUP BY delivery_city, delivery_district
 *   - JOIN branches ON routed_branch_id = branches.id
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Order type discriminator
            $table->enum('order_type', ['pos', 'online'])->default('pos')->after('status');

            // Customer delivery location (captured at checkout)
            $table->string('delivery_city')->nullable()->after('order_type');
            $table->string('delivery_district')->nullable()->after('delivery_city');
            $table->string('delivery_area')->nullable()->after('delivery_district');
            $table->text('delivery_address')->nullable()->after('delivery_area');

            // Optional GPS coordinates from browser geolocation API
            $table->decimal('customer_lat', 10, 7)->nullable()->after('delivery_address');
            $table->decimal('customer_lng', 10, 7)->nullable()->after('customer_lat');

            // Branch routing audit trail
            $table->foreignId('routed_branch_id')
                ->nullable()
                ->after('customer_lng')
                ->constrained('branches')
                ->nullOnDelete();

            $table->enum('routing_method', [
                'coverage_match',  // Customer city matched branch coverage_areas
                'distance',        // Nearest branch by Haversine formula
                'default',         // Branch flagged as is_default = true
                'fallback',        // First active branch (last resort)
            ])->nullable()->after('routed_branch_id');

            $table->decimal('routing_distance_km', 8, 2)->nullable()->after('routing_method');

            // Guest customer info (for online orders without a registered customer)
            $table->string('customer_name')->nullable()->after('routing_distance_km');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->string('customer_email')->nullable()->after('customer_phone');
            $table->text('notes')->nullable()->after('customer_email');

            // Indexes for reporting queries
            $table->index(['company_id', 'order_type'], 'idx_sales_online_orders');
            $table->index(['company_id', 'order_type', 'delivery_city'], 'idx_sales_online_city');
            $table->index(['routed_branch_id', 'order_type'], 'idx_sales_routed_branch');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('idx_sales_online_orders');
            $table->dropIndex('idx_sales_online_city');
            $table->dropIndex('idx_sales_routed_branch');
            $table->dropForeign(['routed_branch_id']);
            $table->dropColumn([
                'order_type',
                'delivery_city',
                'delivery_district',
                'delivery_area',
                'delivery_address',
                'customer_lat',
                'customer_lng',
                'routed_branch_id',
                'routing_method',
                'routing_distance_km',
                'customer_name',
                'customer_phone',
                'customer_email',
                'notes',
            ]);
        });
    }
};
