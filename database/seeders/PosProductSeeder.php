<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PosProductSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::with('branches')->get();

        if ($companies->isEmpty()) {
            $this->command->warn('⚠️ No companies found. Run CompanySeeder first.');
            return;
        }

        foreach ($companies as $company) {
            $this->command->info("Seeding POS products for company: {$company->name}");

            $branches = $company->branches;
            if ($branches->isEmpty()) {
                $this->command->warn("  ⚠️ No branches found for {$company->name}. Skipping product stock seeding.");
                continue;
            }

            $unit = Unit::firstOrCreate(
                ['company_id' => $company->id, 'short_code' => 'pc'],
                ['name' => 'Piece', 'is_active' => true]
            );

            $template = $this->productTemplateForCompany($company);
            foreach ($template as $productData) {
                $category = Category::firstOrCreate(
                    ['company_id' => $company->id, 'name' => $productData['category']],
                    ['slug' => Str::slug($productData['category']), 'is_active' => true]
                );

                $product = Product::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'name' => $productData['name'],
                    ],
                    [
                        'category_id' => $category->id,
                        'description' => $productData['description'],
                        'has_variants' => $productData['has_variants'],
                        'is_bulk' => false,
                        'is_active' => true,
                    ]
                );

                foreach ($productData['variants'] as $index => $variantData) {
                    $sku = strtoupper('POS-' . $company->id . '-' . Str::slug($product->name) . '-' . Str::slug($variantData['name']));
                    $sku = preg_replace('/[^A-Z0-9\-]/', '', $sku);
                    $sku = Str::substr($sku, 0, 60);
                    $barcode = $this->companyBarcode($company, $variantData['barcode'], $index);

                    $variant = ProductVariant::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'name' => $variantData['name'],
                        ],
                        [
                            'sku' => $sku,
                            'barcode' => $barcode,
                            'unit_id' => $unit->id,
                            'cost_price' => $variantData['cost_price'],
                            'selling_price' => $variantData['selling_price'],
                            'reorder_level' => $variantData['reorder_level'],
                            'attributes' => $variantData['attributes'],
                            'is_active' => true,
                        ]
                    );

                    foreach ($branches as $branch) {
                        Stock::updateOrCreate(
                            [
                                'company_id' => $company->id,
                                'branch_id' => $branch->id,
                                'variant_id' => $variant->id,
                            ],
                            [
                                'quantity' => $variantData['stock'],
                                'reorder_level' => $variantData['reorder_level'],
                            ]
                        );
                    }
                }
            }
        }

        $this->command->info('✅ POS product seeding completed.');
    }

    private function companyBarcode(Company $company, string $baseBarcode, int $index = 0): string
    {
        $prefix = strtoupper(Str::slug($company->slug ?? 'tenant'));
        $hash = substr(md5($company->id . '|' . $baseBarcode . '|' . $index), 0, 8);

        return strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', $prefix . $hash), 0, 20));
    }

    private function productTemplateForCompany(Company $company): array
    {
        $slug = $company->businessType?->slug ?? 'general-retail';

        return match ($slug) {
            'fashion-clothing' => [
                [
                    'category' => 'Women Wear',
                    'name' => 'Everyday Cotton T-Shirt',
                    'description' => 'Soft cotton tee with modern fit.',
                    'has_variants' => true,
                    'variants' => [
                        ['name' => 'Size S', 'barcode' => '111111000001', 'cost_price' => 250.00, 'selling_price' => 399.00, 'stock' => 40, 'reorder_level' => 10, 'attributes' => ['Size' => 'S', 'Color' => 'Black']],
                        ['name' => 'Size M', 'barcode' => '111111000002', 'cost_price' => 250.00, 'selling_price' => 399.00, 'stock' => 35, 'reorder_level' => 10, 'attributes' => ['Size' => 'M', 'Color' => 'Black']],
                        ['name' => 'Size L', 'barcode' => '111111000003', 'cost_price' => 250.00, 'selling_price' => 399.00, 'stock' => 30, 'reorder_level' => 10, 'attributes' => ['Size' => 'L', 'Color' => 'Black']],
                    ],
                ],
                [
                    'category' => 'Accessories',
                    'name' => 'Leather Belt',
                    'description' => 'Classic leather belt with polished buckle.',
                    'has_variants' => false,
                    'variants' => [
                        ['name' => 'Default', 'barcode' => '111111000004', 'cost_price' => 200.00, 'selling_price' => 349.00, 'stock' => 25, 'reorder_level' => 8, 'attributes' => []],
                    ],
                ],
            ],
            'electronics' => [
                [
                    'category' => 'Mobile',
                    'name' => 'Smartphone X 128GB',
                    'description' => 'Fast performance and long battery life.',
                    'has_variants' => false,
                    'variants' => [
                        ['name' => 'Default', 'barcode' => '222222000001', 'cost_price' => 15500.00, 'selling_price' => 19999.00, 'stock' => 20, 'reorder_level' => 5, 'attributes' => ['Model' => 'X', 'Storage' => '128GB']],
                    ],
                ],
                [
                    'category' => 'Accessories',
                    'name' => 'Bluetooth Earbuds',
                    'description' => 'Noise-cancelling wireless earbuds.',
                    'has_variants' => false,
                    'variants' => [
                        ['name' => 'Default', 'barcode' => '222222000002', 'cost_price' => 950.00, 'selling_price' => 1499.00, 'stock' => 60, 'reorder_level' => 15, 'attributes' => []],
                    ],
                ],
            ],
            default => [
                [
                    'category' => 'Groceries',
                    'name' => 'Premium Cooking Oil 1L',
                    'description' => 'Pure vegetable oil for daily cooking.',
                    'has_variants' => false,
                    'variants' => [
                        ['name' => 'Default', 'barcode' => '333333000001', 'cost_price' => 180.00, 'selling_price' => 249.00, 'stock' => 80, 'reorder_level' => 20, 'attributes' => []],
                    ],
                ],
                [
                    'category' => 'Stationery',
                    'name' => 'Premium Notebook',
                    'description' => 'A5 notebook with 120 ruled pages.',
                    'has_variants' => false,
                    'variants' => [
                        ['name' => 'Default', 'barcode' => '333333000002', 'cost_price' => 45.00, 'selling_price' => 79.00, 'stock' => 100, 'reorder_level' => 15, 'attributes' => []],
                    ],
                ],
            ],
        };
    }
}
