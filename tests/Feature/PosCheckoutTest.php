<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\Shift;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function makeSalesmanWithStock(int $initialQty = 10, float $sellingPrice = 100.00): array
    {
        $company = Company::create([
            'name' => 'Test Retail Co',
            'slug' => 'test-retail-co-' . uniqid(),
            'email' => 'retail-' . uniqid() . '@example.com',
            'status' => 'active',
        ]);

        $branch = Branch::create([
            'company_id' => $company->id,
            'name' => 'Main Branch',
            'status' => 'active',
        ]);

        Role::firstOrCreate(['name' => 'Salesman', 'guard_name' => 'web']);

        $user = User::create([
            'name' => 'Test Salesman',
            'email' => 'salesman-' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'email_verified_at' => now(),
        ]);
        $user->assignRole('Salesman');

        $category = Category::create(['company_id' => $company->id, 'name' => 'General']);

        $product = Product::create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'name' => 'Test Widget',
            'has_variants' => false,
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-' . uniqid(),
            'cost_price' => 50.00,
            'selling_price' => $sellingPrice,
            'is_active' => true,
        ]);

        $stock = Stock::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'variant_id' => $variant->id,
            'quantity' => $initialQty,
            'reorder_level' => 2,
        ]);

        Shift::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'opened_by' => $user->id,
            'opening_balance' => 0,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        return compact('company', 'branch', 'user', 'variant', 'stock');
    }

    public function test_checkout_deducts_stock_and_creates_sale(): void
    {
        ['user' => $user, 'variant' => $variant, 'stock' => $stock] = $this->makeSalesmanWithStock(10, 100.00);

        $response = $this->actingAs($user)->postJson(route('branch.pos.checkout'), [
            'items' => [
                ['variant_id' => $variant->id, 'qty' => 3],
            ],
            'payment_method' => 'cash',
            'received_amount' => 300,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $stock->refresh();
        $this->assertSame(7, $stock->quantity);

        $sale = Sale::first();
        $this->assertNotNull($sale);
        $this->assertSame(300.0, (float) $sale->total_amount);
        $this->assertSame(1, $sale->items()->count());

        $this->assertDatabaseHas('stock_movements', [
            'variant_id' => $variant->id,
            'type' => 'sale_out',
            'quantity' => -3,
        ]);
    }

    public function test_checkout_ignores_client_supplied_price(): void
    {
        // Server must always price from the DB (selling_price = 100), never
        // from a client-supplied field — even if the request smuggles one in.
        ['user' => $user, 'variant' => $variant] = $this->makeSalesmanWithStock(10, 100.00);

        $response = $this->actingAs($user)->postJson(route('branch.pos.checkout'), [
            'items' => [
                ['variant_id' => $variant->id, 'qty' => 1, 'price' => 1],
            ],
            'payment_method' => 'cash',
            'received_amount' => 100,
        ]);

        $response->assertOk();

        $sale = Sale::first();
        $this->assertSame(100.0, (float) $sale->total_amount);
        $this->assertSame(100.0, (float) $sale->items()->first()->unit_price);
    }

    public function test_checkout_rejects_insufficient_stock_without_oversell(): void
    {
        ['user' => $user, 'variant' => $variant, 'stock' => $stock] = $this->makeSalesmanWithStock(2, 100.00);

        $response = $this->actingAs($user)->postJson(route('branch.pos.checkout'), [
            'items' => [
                ['variant_id' => $variant->id, 'qty' => 5],
            ],
            'payment_method' => 'cash',
            'received_amount' => 500,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);

        $stock->refresh();
        $this->assertSame(2, $stock->quantity);
        $this->assertSame(0, Sale::count());
    }

    public function test_checkout_requires_an_open_shift(): void
    {
        ['user' => $user, 'variant' => $variant] = $this->makeSalesmanWithStock(10, 100.00);

        // Close the shift that was opened in setup.
        Shift::where('opened_by', $user->id)->update(['status' => 'closed', 'closed_at' => now()]);

        $response = $this->actingAs($user)->postJson(route('branch.pos.checkout'), [
            'items' => [
                ['variant_id' => $variant->id, 'qty' => 1],
            ],
            'payment_method' => 'cash',
            'received_amount' => 100,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['success' => false]);
        $this->assertSame(0, Sale::count());
    }

    public function test_checkout_rejects_customer_from_another_company(): void
    {
        ['user' => $user, 'variant' => $variant] = $this->makeSalesmanWithStock(10, 100.00);

        $otherCompany = Company::create([
            'name' => 'Other Co',
            'slug' => 'other-co-' . uniqid(),
            'email' => 'other-' . uniqid() . '@example.com',
            'status' => 'active',
        ]);

        $foreignCustomer = Customer::create([
            'company_id' => $otherCompany->id,
            'name' => 'Foreign Customer',
        ]);

        $response = $this->actingAs($user)->postJson(route('branch.pos.checkout'), [
            'items' => [
                ['variant_id' => $variant->id, 'qty' => 1],
            ],
            'customer_id' => $foreignCustomer->id,
            'payment_method' => 'cash',
            'received_amount' => 100,
        ]);

        $response->assertStatus(422);
        $this->assertSame(0, Sale::count());
    }

    public function test_checkout_endpoint_is_rate_limited(): void
    {
        ['user' => $user] = $this->makeSalesmanWithStock(10, 100.00);

        // Invalid payload (no items) still passes through the throttle
        // middleware before hitting validation, so we can exhaust the
        // limiter cheaply without touching stock.
        for ($i = 0; $i < 60; $i++) {
            $this->actingAs($user)->postJson(route('branch.pos.checkout'), []);
        }

        $response = $this->actingAs($user)->postJson(route('branch.pos.checkout'), []);

        $response->assertStatus(429);
    }
}
