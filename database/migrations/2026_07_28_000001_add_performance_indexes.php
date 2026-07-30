<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Performance Indexes for Multi-Tenant SaaS
 *
 * Why composite indexes?
 * With 500 companies each having thousands of records,
 * every query MUST filter by company_id first.
 * Without these indexes, MySQL does a full table scan
 * which causes slow queries and site crashes under load.
 *
 * Rule: Every table with company_id needs a composite index
 * on (company_id, <most_queried_column>)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── companies table ──────────────────────────────────────────
        // Domain resolution is the most critical query - runs on EVERY request
        Schema::table('companies', function (Blueprint $table) {
            if (! $this->indexExists('companies', 'companies_custom_domain_index')) {
                $table->index('custom_domain', 'companies_custom_domain_index');
            }
            if (! $this->indexExists('companies', 'companies_subdomain_index')) {
                $table->index('subdomain', 'companies_subdomain_index');
            }
            if (! $this->indexExists('companies', 'companies_status_index')) {
                $table->index('status', 'companies_status_index');
            }
        });

        // ── products table ───────────────────────────────────────────
        Schema::table('products', function (Blueprint $table) {
            if (! $this->indexExists('products', 'products_company_active_idx')) {
                $table->index(['company_id', 'is_active'], 'products_company_active_idx');
            }
            if (! $this->indexExists('products', 'products_company_category_idx')) {
                $table->index(['company_id', 'category_id'], 'products_company_category_idx');
            }
        });

        // ── product_variants table ───────────────────────────────────
        Schema::table('product_variants', function (Blueprint $table) {
            if (! $this->indexExists('product_variants', 'variants_product_active_idx')) {
                $table->index(['product_id', 'is_active'], 'variants_product_active_idx');
            }
        });

        // ── stocks table ─────────────────────────────────────────────
        Schema::table('stocks', function (Blueprint $table) {
            if (! $this->indexExists('stocks', 'stocks_company_branch_idx')) {
                $table->index(['company_id', 'branch_id'], 'stocks_company_branch_idx');
            }
        });

        // ── sales table ──────────────────────────────────────────────
        // Most queried table - POS checkout + reports
        Schema::table('sales', function (Blueprint $table) {
            if (! $this->indexExists('sales', 'sales_company_branch_date_idx')) {
                $table->index(['company_id', 'branch_id', 'created_at'], 'sales_company_branch_date_idx');
            }
            if (! $this->indexExists('sales', 'sales_company_status_idx')) {
                $table->index(['company_id', 'status'], 'sales_company_status_idx');
            }
        });

        // ── sale_items table ─────────────────────────────────────────
        Schema::table('sale_items', function (Blueprint $table) {
            if (! $this->indexExists('sale_items', 'sale_items_sale_idx')) {
                $table->index('sale_id', 'sale_items_sale_idx');
            }
        });

        // ── purchases table ──────────────────────────────────────────
        Schema::table('purchases', function (Blueprint $table) {
            if (! $this->indexExists('purchases', 'purchases_company_branch_date_idx')) {
                $table->index(['company_id', 'branch_id', 'purchase_date'], 'purchases_company_branch_date_idx');
            }
        });

        // ── customers table ──────────────────────────────────────────
        Schema::table('customers', function (Blueprint $table) {
            if (! $this->indexExists('customers', 'customers_company_idx')) {
                $table->index('company_id', 'customers_company_idx');
            }
        });

        // ── suppliers table ──────────────────────────────────────────
        Schema::table('suppliers', function (Blueprint $table) {
            if (! $this->indexExists('suppliers', 'suppliers_company_idx')) {
                $table->index('company_id', 'suppliers_company_idx');
            }
        });

        // ── expenses table ───────────────────────────────────────────
        Schema::table('expenses', function (Blueprint $table) {
            if (! $this->indexExists('expenses', 'expenses_company_branch_date_idx')) {
                $table->index(['company_id', 'branch_id', 'expense_date'], 'expenses_company_branch_date_idx');
            }
        });

        // ── payments table ───────────────────────────────────────────
        Schema::table('payments', function (Blueprint $table) {
            if (! $this->indexExists('payments', 'payments_company_date_idx')) {
                $table->index(['company_id', 'payment_date'], 'payments_company_date_idx');
            }
        });

        // ── stock_movements table ────────────────────────────────────
        Schema::table('stock_movements', function (Blueprint $table) {
            if (! $this->indexExists('stock_movements', 'stock_movements_company_branch_idx')) {
                $table->index(['company_id', 'branch_id', 'created_at'], 'stock_movements_company_branch_idx');
            }
        });

        // ── categories table ─────────────────────────────────────────
        Schema::table('categories', function (Blueprint $table) {
            if (! $this->indexExists('categories', 'categories_company_active_idx')) {
                $table->index(['company_id', 'is_active'], 'categories_company_active_idx');
            }
        });

        // ── branches table ───────────────────────────────────────────
        Schema::table('branches', function (Blueprint $table) {
            if (! $this->indexExists('branches', 'branches_company_status_idx')) {
                $table->index(['company_id', 'status'], 'branches_company_status_idx');
            }
        });

        // ── users table ──────────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            if (! $this->indexExists('users', 'users_company_branch_idx')) {
                $table->index(['company_id', 'branch_id'], 'users_company_branch_idx');
            }
        });

        // ── shifts table ─────────────────────────────────────────────
        Schema::table('shifts', function (Blueprint $table) {
            if (! $this->indexExists('shifts', 'shifts_branch_status_idx')) {
                $table->index(['branch_id', 'status'], 'shifts_branch_status_idx');
            }
        });

        // ── subscriptions table ──────────────────────────────────────
        Schema::table('subscriptions', function (Blueprint $table) {
            if (! $this->indexExists('subscriptions', 'subscriptions_company_status_idx')) {
                $table->index(['company_id', 'status'], 'subscriptions_company_status_idx');
            }
        });
    }

    public function down(): void
    {
        $indexes = [
            'companies'       => ['companies_custom_domain_index', 'companies_subdomain_index', 'companies_status_index'],
            'products'        => ['products_company_active_idx', 'products_company_category_idx'],
            'product_variants' => ['variants_product_active_idx'],
            'stocks'          => ['stocks_company_branch_idx'],
            'sales'           => ['sales_company_branch_date_idx', 'sales_company_status_idx'],
            'sale_items'      => ['sale_items_sale_idx'],
            'purchases'       => ['purchases_company_branch_date_idx'],
            'customers'       => ['customers_company_idx'],
            'suppliers'       => ['suppliers_company_idx'],
            'expenses'        => ['expenses_company_branch_date_idx'],
            'payments'        => ['payments_company_date_idx'],
            'stock_movements' => ['stock_movements_company_branch_idx'],
            'categories'      => ['categories_company_active_idx'],
            'branches'        => ['branches_company_status_idx'],
            'users'           => ['users_company_branch_idx'],
            'shifts'          => ['shifts_branch_status_idx'],
            'subscriptions'   => ['subscriptions_company_status_idx'],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            Schema::table($table, function (Blueprint $blueprint) use ($tableIndexes) {
                foreach ($tableIndexes as $index) {
                    try {
                        $blueprint->dropIndex($index);
                    } catch (\Throwable $e) {
                        // ignore if index doesn't exist
                    }
                }
            });
        }
    }

    protected function indexExists(string $table, string $indexName): bool
    {
        try {
            $indexes = \Illuminate\Support\Facades\DB::select(
                "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
                [$indexName]
            );
            return count($indexes) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
};
