<?php

namespace Tests\Feature;

use App\Models\CashAccount;
use App\Models\Category;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\LoanAuthority;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompanyAdmin(): array
    {
        $company = Company::create([
            'name' => 'Company ' . uniqid(),
            'slug' => 'company-' . uniqid(),
            'email' => uniqid() . '@example.com',
            // Trial status bypasses the CheckSubscription middleware so these
            // tests exercise tenant isolation, not subscription gating.
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(30),
        ]);

        Role::firstOrCreate(['name' => 'Company Admin', 'guard_name' => 'web']);

        $admin = User::create([
            'name' => 'Admin ' . uniqid(),
            'email' => uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Company Admin');

        return [$company, $admin];
    }

    public function test_company_admin_cannot_view_another_companys_product(): void
    {
        [$companyA, $adminA] = $this->makeCompanyAdmin();
        [$companyB] = $this->makeCompanyAdmin();

        $category = Category::create(['company_id' => $companyB->id, 'name' => 'General']);
        $foreignProduct = Product::create([
            'company_id' => $companyB->id,
            'category_id' => $category->id,
            'name' => 'Company B Widget',
        ]);

        $this->actingAs($adminA)
            ->get(route('company.products.show', $foreignProduct->id))
            ->assertForbidden();

        $this->actingAs($adminA)
            ->get(route('company.products.edit', $foreignProduct->id))
            ->assertForbidden();

        $this->actingAs($adminA)
            ->delete(route('company.products.destroy', $foreignProduct->id))
            ->assertForbidden();

        // Sanity check: the product was never touched.
        $this->assertDatabaseHas('products', ['id' => $foreignProduct->id, 'company_id' => $companyB->id]);
    }

    public function test_company_admin_cannot_view_another_companys_quotation(): void
    {
        [$companyA, $adminA] = $this->makeCompanyAdmin();
        [$companyB] = $this->makeCompanyAdmin();

        $foreignQuotation = Quotation::create([
            'company_id' => $companyB->id,
            'quotation_no' => 'QT-FOREIGN-1',
            'subtotal' => 100,
            'discount' => 0,
            'total_amount' => 100,
            'status' => 'draft',
        ]);

        $this->actingAs($adminA)
            ->get(route('company.quotations.show', $foreignQuotation->id))
            ->assertNotFound();
    }

    public function test_company_admin_cannot_increment_another_companys_employee_salary(): void
    {
        [$companyA, $adminA] = $this->makeCompanyAdmin();
        [$companyB] = $this->makeCompanyAdmin();

        $department = Department::create(['company_id' => $companyB->id, 'name' => 'Ops']);
        $employee = Employee::create([
            'company_id' => $companyB->id,
            'department_id' => $department->id,
            'name' => 'Foreign Employee',
            'join_date' => now()->subYear(),
            'salary' => 1000,
            'status' => 'active',
        ]);

        $this->actingAs($adminA)->post(route('company.employees.increments.store'), [
            'employee_id' => $employee->id,
            'amount' => 500,
            'effective_date' => now()->toDateString(),
        ])->assertSessionHasErrors('employee_id');

        $employee->refresh();
        $this->assertSame(1000.0, (float) $employee->salary);
    }

    public function test_company_admin_cannot_pay_another_companys_loan(): void
    {
        [$companyA, $adminA] = $this->makeCompanyAdmin();
        [$companyB] = $this->makeCompanyAdmin();

        $authority = LoanAuthority::create(['company_id' => $companyB->id, 'name' => 'Foreign Bank']);
        $loan = Loan::create([
            'company_id' => $companyB->id,
            'loan_authority_id' => $authority->id,
            'amount' => 5000,
            'loan_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $this->actingAs($adminA)->post(route('company.loans.payments.store'), [
            'loan_id' => $loan->id,
            'amount' => 1000,
            'payment_date' => now()->toDateString(),
        ])->assertSessionHasErrors('loan_id');

        $this->assertSame(0, $loan->payments()->count());
    }

    public function test_company_admin_cannot_transfer_from_another_companys_cash_account(): void
    {
        [$companyA, $adminA] = $this->makeCompanyAdmin();
        [$companyB] = $this->makeCompanyAdmin();

        $foreignFrom = CashAccount::create(['company_id' => $companyB->id, 'name' => 'Foreign Cash', 'current_balance' => 1000]);
        $foreignTo = CashAccount::create(['company_id' => $companyB->id, 'name' => 'Foreign Bank', 'current_balance' => 0]);

        $this->actingAs($adminA)->post(route('company.cashbook.transfers.store'), [
            'from_account_id' => $foreignFrom->id,
            'to_account_id' => $foreignTo->id,
            'amount' => 500,
            'transfer_date' => now()->toDateString(),
        ])->assertSessionHasErrors('from_account_id');

        $foreignFrom->refresh();
        $this->assertSame(1000.0, (float) $foreignFrom->current_balance);
    }
}
