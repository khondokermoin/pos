<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;

class DemoProductSeeder extends Seeder
{
    public function run(): void
    {
        // ১. প্রথম কোম্পানি খুঁজে বের করা
        $company = Company::first();
        if (!$company) {
            $this->command->error('❌ No company found.');
            return;
        }

        // ২. ব্রাঞ্চ খুঁজে বের করা, না থাকলে তৈরি করা (is_active বাদ দিয়ে)
        $branch = Branch::where('company_id', $company->id)->first();
        if (!$branch) {
            $branch = Branch::create([
                'company_id' => $company->id,
                'name' => 'Main Branch (Demo)',
                'address' => 'Demo Address',
                'phone' => '01700000000',
            ]);
        }

        // ৩. ক্যাটাগরি তৈরি (description বাদ দিয়ে শুধু name দিয়ে)
        $category = Category::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Demo Electronics']
        );

        // ৪. প্রোডাক্ট তৈরি (description এবং is_active বাদ দিয়ে শুধু প্রয়োজনীয় ফিল্ড)
        $product = Product::create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'name' => 'Wireless Optical Mouse',
        ]);

        // ৫. প্রোডাক্ট ভ্যারিয়েন্ট তৈরি
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'MOUSE-WL-001',
            'barcode' => '1234567890123',
            'name' => 'Black',
            'cost_price' => 350.00,
            'selling_price' => 550.00,
        ]);

        // ৬. স্টক যোগ করা
        Stock::updateOrCreate(
            [
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'variant_id' => $variant->id,
            ],
            [
                'quantity' => 50,
                'reorder_level' => 10,
            ]
        );

        $this->command->info('✅ Demo setup successful!');
        $this->command->info('🏷️ Barcode to scan/type: 1234567890123');
        $this->command->info('🏢 Added to Branch: ' . $branch->name);
    }
}
