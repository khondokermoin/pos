<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Multi-Tenant SaaS Seeding Hierarchy
     * ─────────────────────────────────────────────────────────────────────────
     *  Level 0 │ Foundation data  → Plans, Roles & Permissions
     *  Level 1 │ Super Admin      → superadmin@system.com  (company_id = null)
     *  Level 2 │ Companies        → 3 companies + 1 Company Admin each
     *  Level 3 │ Branches         → 2-3 branches per company + 1 Manager each
     *  Level 4 │ POS Salesmen     → 1-3 Salesman users per branch (= POS PCs)
     * ─────────────────────────────────────────────────────────────────────────
     *
     * Run with:
     *   php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════════════╗');
        $this->command->info('║     Cloud POS — Multi-Tenant Database Seeder     ║');
        $this->command->info('╚══════════════════════════════════════════════════╝');
        $this->command->info('');

        // ── Level 0: Foundation ───────────────────────────────────────────────
        // Plans must exist before companies (companies.plan_id FK)
        // Roles & Permissions must exist before any user is assigned a role
        $this->command->info('▶ [0/5] Seeding Plans...');
        $this->call(PlanSeeder::class);

        $this->command->info('▶ [1/5] Seeding Roles & Permissions...');
        $this->call(RolePermissionSeeder::class);

        // ── Level 1: Super Admin ──────────────────────────────────────────────
        $this->command->info('▶ [2/5] Seeding Super Admin...');
        $this->call(SuperAdminSeeder::class);

        // ── Level 2: Companies + Company Admins ───────────────────────────────
        $this->command->info('▶ [3/5] Seeding Companies & Company Admins...');
        $this->call(CompanySeeder::class);

        // ── Level 3 + 4: Branches + Managers + Salesmen (POS PCs) ────────────
        $this->command->info('▶ [4/6] Seeding Branches, Managers & Salesmen...');
        $this->call(BranchSeeder::class);

        // ── Level 5: POS products for terminal visibility ─────────────────────
        $this->command->info('▶ [5/6] Seeding POS products...');
        $this->call(PosProductSeeder::class);

        // ── Optional: Master / reference data ────────────────────────────────
        $this->command->info('▶ [6/6] Seeding master reference data...');
        $this->call([
            BusinessTypeSeeder::class,
            CategorySeeder::class,
            SettingSeeder::class,
            EmailTemplateSeeder::class,
        ]);

        // ── Summary ───────────────────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════════════╗');
        $this->command->info('║            ✅  Seeding Complete!                 ║');
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info('║  LEVEL 1 — Super Admin                           ║');
        $this->command->info('║    Email    : superadmin@system.com              ║');
        $this->command->info('║    Password : password                           ║');
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info('║  LEVEL 2 — Company Admins                        ║');
        $this->command->info('║    admin@alpharetail.example   / password        ║');
        $this->command->info('║    admin@betaelectronics.example / password      ║');
        $this->command->info('║    admin@gammafashion.example  / password        ║');
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info('║  LEVEL 3 — Branch Managers  (see BranchSeeder)   ║');
        $this->command->info('║    manager.motijheel@alpharetail.example         ║');
        $this->command->info('║    manager.mirpur@alpharetail.example            ║');
        $this->command->info('║    manager.uttara@alpharetail.example            ║');
        $this->command->info('║    manager.gulshan@betaelectronics.example       ║');
        $this->command->info('║    manager.dhanmondi@betaelectronics.example     ║');
        $this->command->info('║    manager.banani@gammafashion.example           ║');
        $this->command->info('║    manager.bashundhara@gammafashion.example      ║');
        $this->command->info('║    All passwords: password                       ║');
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info('║  LEVEL 4 — Salesmen / POS PCs  (per branch)      ║');
        $this->command->info('║    salesman[1-3].<branch>@<company>.example      ║');
        $this->command->info('║    All passwords: password                       ║');
        $this->command->info('╚══════════════════════════════════════════════════╝');
        $this->command->info('');
    }
}
