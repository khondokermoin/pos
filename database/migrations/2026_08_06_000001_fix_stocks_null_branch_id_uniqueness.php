<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fix: stocks table NULL branch_id uniqueness gap (Phase 1 central-warehouse regression)
 *
 * DATABASE: MariaDB 10.4.32
 *
 * PROBLEM:
 *   Phase 1 made stocks.branch_id nullable to support central-warehouse stock.
 *   MariaDB (like MySQL) treats each NULL as distinct in a UNIQUE index, so the
 *   existing unique index (branch_id, variant_id) does NOT prevent two rows with
 *   the same variant_id and branch_id = NULL from being created concurrently.
 *
 * ACTUAL SCHEMA (verified via `php artisan db:table stocks`):
 *   Unique index : stocks_branch_id_variant_id_unique (branch_id, variant_id)
 *   Foreign key  : stocks_branch_id_foreign → branches.id (restrict/cascade)
 *
 * FIX APPROACH:
 *   Generated columns in MariaDB 10.4 use the syntax:
 *     col_name type AS (expr) VIRTUAL
 *   NOT the MySQL 5.7+ syntax:
 *     col_name type GENERATED ALWAYS AS (expr) VIRTUAL
 *
 *   Steps:
 *   1. Merge any duplicate central-warehouse rows.
 *   2. Drop FK (required before dropping the unique index it supports).
 *   3. Drop old unique index.
 *   4. Add generated column using MariaDB syntax.
 *   5. Add new unique index (company_id, variant_id, branch_id_key).
 *   6. Re-add FK on branch_id.
 *
 *   Each step is wrapped in a try/catch so re-running after a partial failure
 *   is safe (idempotent).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Step 1: Merge any duplicate central-warehouse rows ────────────────
        $duplicates = DB::table('stocks')
            ->selectRaw('company_id, variant_id, SUM(quantity) as total_qty, MIN(id) as keep_id')
            ->whereNull('branch_id')
            ->groupBy('company_id', 'variant_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            DB::table('stocks')
                ->where('id', $dup->keep_id)
                ->update(['quantity' => $dup->total_qty]);

            DB::table('stocks')
                ->where('company_id', $dup->company_id)
                ->where('variant_id', $dup->variant_id)
                ->whereNull('branch_id')
                ->where('id', '!=', $dup->keep_id)
                ->delete();
        }

        // ── Step 2: Drop FK (must happen before dropping the unique index) ───
        try {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropForeign('stocks_branch_id_foreign');
            });
        } catch (\Throwable $e) {
            // Already dropped in a previous partial run — continue.
        }

        // ── Step 3: Drop old unique index ────────────────────────────────────
        try {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropUnique('stocks_branch_id_variant_id_unique');
            });
        } catch (\Throwable $e) {
            // Already dropped in a previous partial run — continue.
        }

        // ── Step 4: Add generated column (MariaDB 10.4 syntax) ───────────────
        // MariaDB syntax: col_name type AS (expr) VIRTUAL
        // MySQL 5.7+ syntax: col_name type GENERATED ALWAYS AS (expr) VIRTUAL
        // We use the MariaDB form which is also accepted by MySQL 5.7+.
        try {
            DB::statement('ALTER TABLE stocks ADD COLUMN branch_id_key BIGINT UNSIGNED AS (COALESCE(branch_id, 0)) VIRTUAL');
        } catch (\Throwable $e) {
            // Column may already exist from a previous partial run — continue.
            if (! str_contains($e->getMessage(), 'Duplicate column')) {
                throw $e;
            }
        }

        // ── Step 5: Add new unique index (company-scoped) ────────────────────
        try {
            DB::statement('ALTER TABLE stocks ADD UNIQUE INDEX stocks_company_variant_branch_unique (company_id, variant_id, branch_id_key)');
        } catch (\Throwable $e) {
            // Index may already exist from a previous partial run — continue.
            if (! str_contains($e->getMessage(), 'Duplicate key name')) {
                throw $e;
            }
        }

        // ── Step 6: Re-add FK on branch_id ───────────────────────────────────
        try {
            Schema::table('stocks', function (Blueprint $table) {
                $table->foreign('branch_id', 'stocks_branch_id_foreign')
                    ->references('id')
                    ->on('branches')
                    ->onUpdate('restrict')
                    ->onDelete('cascade');
            });
        } catch (\Throwable $e) {
            // FK may already exist from a previous partial run — continue.
            if (! str_contains($e->getMessage(), 'already exists')) {
                throw $e;
            }
        }
    }

    public function down(): void
    {
        // Drop FK first
        try {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropForeign('stocks_branch_id_foreign');
            });
        } catch (\Throwable) {
        }

        // Drop new index
        try {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropUnique('stocks_company_variant_branch_unique');
            });
        } catch (\Throwable) {
        }

        // Drop generated column
        try {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropColumn('branch_id_key');
            });
        } catch (\Throwable) {
        }

        // Restore original unique index
        try {
            Schema::table('stocks', function (Blueprint $table) {
                $table->unique(['branch_id', 'variant_id'], 'stocks_branch_id_variant_id_unique');
            });
        } catch (\Throwable) {
        }

        // Re-add FK
        try {
            Schema::table('stocks', function (Blueprint $table) {
                $table->foreign('branch_id', 'stocks_branch_id_foreign')
                    ->references('id')
                    ->on('branches')
                    ->onUpdate('restrict')
                    ->onDelete('cascade');
            });
        } catch (\Throwable) {
        }
    }
};
