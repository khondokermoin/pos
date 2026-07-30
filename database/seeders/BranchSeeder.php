<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BranchSeeder extends Seeder
{
    /**
     * Level 3 — Branches + Branch Managers.
     * Level 4 — Salesman users (POS counters) per branch.
     *
     * Schema columns used (verified from migrations):
     *   branches : id, company_id, name, email, phone, address, status, manager_id
     *   users    : id, name, email, password, company_id, branch_id
     *
     * There is NO separate registers/counters table.
     * Each "POS PC / counter" is represented by a Salesman user tied to a branch.
     */
    public function run(): void
    {
        // ── Branch definitions keyed by company slug ──────────────────────────
        $branchMap = [
            'alpha-retail-ltd' => [
                [
                    'branch' => [
                        'name'    => 'Alpha Retail — Motijheel Branch',
                        'email'   => 'motijheel@alpharetail.example',
                        'phone'   => '01722000101',
                        'address' => '12 Motijheel C/A, Dhaka',
                        'status'  => 'active',
                    ],
                    'manager' => [
                        'name'  => 'Alpha Motijheel Manager',
                        'email' => 'manager.motijheel@alpharetail.example',
                    ],
                    'salesmen' => [
                        ['name' => 'Alpha Salesman 1 (Motijheel)', 'email' => 'salesman1.motijheel@alpharetail.example'],
                        ['name' => 'Alpha Salesman 2 (Motijheel)', 'email' => 'salesman2.motijheel@alpharetail.example'],
                    ],
                ],
                [
                    'branch' => [
                        'name'    => 'Alpha Retail — Mirpur Branch',
                        'email'   => 'mirpur@alpharetail.example',
                        'phone'   => '01722000102',
                        'address' => '5 Mirpur Road, Dhaka',
                        'status'  => 'active',
                    ],
                    'manager' => [
                        'name'  => 'Alpha Mirpur Manager',
                        'email' => 'manager.mirpur@alpharetail.example',
                    ],
                    'salesmen' => [
                        ['name' => 'Alpha Salesman 1 (Mirpur)', 'email' => 'salesman1.mirpur@alpharetail.example'],
                        ['name' => 'Alpha Salesman 2 (Mirpur)', 'email' => 'salesman2.mirpur@alpharetail.example'],
                        ['name' => 'Alpha Salesman 3 (Mirpur)', 'email' => 'salesman3.mirpur@alpharetail.example'],
                    ],
                ],
                [
                    'branch' => [
                        'name'    => 'Alpha Retail — Uttara Branch',
                        'email'   => 'uttara@alpharetail.example',
                        'phone'   => '01722000103',
                        'address' => '10 Uttara Sector 7, Dhaka',
                        'status'  => 'active',
                    ],
                    'manager' => [
                        'name'  => 'Alpha Uttara Manager',
                        'email' => 'manager.uttara@alpharetail.example',
                    ],
                    'salesmen' => [
                        ['name' => 'Alpha Salesman 1 (Uttara)', 'email' => 'salesman1.uttara@alpharetail.example'],
                    ],
                ],
            ],

            'beta-electronics-co' => [
                [
                    'branch' => [
                        'name'    => 'Beta Electronics — Gulshan Branch',
                        'email'   => 'gulshan@betaelectronics.example',
                        'phone'   => '01733000201',
                        'address' => '45 Gulshan Avenue, Dhaka',
                        'status'  => 'active',
                    ],
                    'manager' => [
                        'name'  => 'Beta Gulshan Manager',
                        'email' => 'manager.gulshan@betaelectronics.example',
                    ],
                    'salesmen' => [
                        ['name' => 'Beta Salesman 1 (Gulshan)', 'email' => 'salesman1.gulshan@betaelectronics.example'],
                        ['name' => 'Beta Salesman 2 (Gulshan)', 'email' => 'salesman2.gulshan@betaelectronics.example'],
                    ],
                ],
                [
                    'branch' => [
                        'name'    => 'Beta Electronics — Dhanmondi Branch',
                        'email'   => 'dhanmondi@betaelectronics.example',
                        'phone'   => '01733000202',
                        'address' => '22 Dhanmondi Road 27, Dhaka',
                        'status'  => 'active',
                    ],
                    'manager' => [
                        'name'  => 'Beta Dhanmondi Manager',
                        'email' => 'manager.dhanmondi@betaelectronics.example',
                    ],
                    'salesmen' => [
                        ['name' => 'Beta Salesman 1 (Dhanmondi)', 'email' => 'salesman1.dhanmondi@betaelectronics.example'],
                        ['name' => 'Beta Salesman 2 (Dhanmondi)', 'email' => 'salesman2.dhanmondi@betaelectronics.example'],
                        ['name' => 'Beta Salesman 3 (Dhanmondi)', 'email' => 'salesman3.dhanmondi@betaelectronics.example'],
                    ],
                ],
            ],

            'gamma-fashion-house' => [
                [
                    'branch' => [
                        'name'    => 'Gamma Fashion — Banani Branch',
                        'email'   => 'banani@gammafashion.example',
                        'phone'   => '01744000301',
                        'address' => '78 Banani Road, Dhaka',
                        'status'  => 'active',
                    ],
                    'manager' => [
                        'name'  => 'Gamma Banani Manager',
                        'email' => 'manager.banani@gammafashion.example',
                    ],
                    'salesmen' => [
                        ['name' => 'Gamma Salesman 1 (Banani)', 'email' => 'salesman1.banani@gammafashion.example'],
                        ['name' => 'Gamma Salesman 2 (Banani)', 'email' => 'salesman2.banani@gammafashion.example'],
                    ],
                ],
                [
                    'branch' => [
                        'name'    => 'Gamma Fashion — Bashundhara Branch',
                        'email'   => 'bashundhara@gammafashion.example',
                        'phone'   => '01744000302',
                        'address' => 'Bashundhara City, Dhaka',
                        'status'  => 'active',
                    ],
                    'manager' => [
                        'name'  => 'Gamma Bashundhara Manager',
                        'email' => 'manager.bashundhara@gammafashion.example',
                    ],
                    'salesmen' => [
                        ['name' => 'Gamma Salesman 1 (Bashundhara)', 'email' => 'salesman1.bashundhara@gammafashion.example'],
                        ['name' => 'Gamma Salesman 2 (Bashundhara)', 'email' => 'salesman2.bashundhara@gammafashion.example'],
                        ['name' => 'Gamma Salesman 3 (Bashundhara)', 'email' => 'salesman3.bashundhara@gammafashion.example'],
                    ],
                ],
            ],
        ];

        foreach ($branchMap as $companySlug => $branches) {
            // Fetch the company created by CompanySeeder
            $company = Company::where('slug', $companySlug)->first();

            if (! $company) {
                $this->command->warn("  ⚠️  Company [{$companySlug}] not found — skipping.");
                continue;
            }

            $this->command->info("  🏢 Seeding branches for: {$company->name}");

            foreach ($branches as $branchData) {
                // ── Level 3: Create / update the Branch ───────────────────────
                $branch = Branch::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'name'       => $branchData['branch']['name'],
                    ],
                    array_merge($branchData['branch'], ['company_id' => $company->id])
                );

                // ── Level 3: Create / update the Branch Manager ───────────────
                // Manager has both company_id AND branch_id set
                $manager = User::updateOrCreate(
                    ['email' => $branchData['manager']['email']],
                    [
                        'name'       => $branchData['manager']['name'],
                        'password'   => Hash::make('password'),
                        'company_id' => $company->id,
                        'branch_id'  => $branch->id,
                    ]
                );

                $manager->syncRoles(['Manager']);

                // Back-fill manager_id on the branch (branches.manager_id → users.id)
                $branch->manager_id = $manager->id;
                $branch->save();

                $this->command->info(
                    "    ✅ Branch: [{$branch->name}]  Manager: [{$manager->email}]"
                );

                // ── Level 4: Create / update Salesman users (POS counters) ────
                // Each salesman = one POS PC / counter at this branch
                foreach ($branchData['salesmen'] as $salesmanData) {
                    $salesman = User::updateOrCreate(
                        ['email' => $salesmanData['email']],
                        [
                            'name'       => $salesmanData['name'],
                            'password'   => Hash::make('password'),
                            'company_id' => $company->id,
                            'branch_id'  => $branch->id,
                        ]
                    );

                    $salesman->syncRoles(['Salesman']);

                    $this->command->info(
                        "       🖥  Salesman (POS): [{$salesman->email}]"
                    );
                }
            }
        }

        $this->command->info('✅ BranchSeeder completed — all branches, managers & salesmen seeded.');
    }
}
