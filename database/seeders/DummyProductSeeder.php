<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Branch;
use App\Models\BusinessType;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\Unit;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = FakerFactory::create();

        $companies = Company::with('branches')->get();

        $basicUnits = [
            ['name' => 'Piece', 'short_code' => 'pc'],
            ['name' => 'Kg', 'short_code' => 'kg'],
            ['name' => 'Box', 'short_code' => 'box'],
        ];

        $genericBrands = ['Nimbus', 'CoreFlex', 'PrimePro', 'Evergreen', 'Apex'];

        foreach ($companies as $company) {
            foreach ($basicUnits as $unitData) {
                Unit::firstOrCreate(
                    [
                        'company_id' => $company->id,
                        'short_code' => $unitData['short_code'],
                    ],
                    [
                        'name' => $unitData['name'],
                        'is_active' => true,
                    ]
                );
            }

            foreach ($genericBrands as $brandName) {
                Brand::firstOrCreate(
                    [
                        'company_id' => $company->id,
                        'name' => $brandName,
                    ],
                    [
                        'is_active' => true,
                    ]
                );
            }

            $branches = $company->branches;
            if ($branches->isEmpty()) {
                $branches = collect([
                    Branch::firstOrCreate(
                        [
                            'company_id' => $company->id,
                            'name' => 'Main Branch',
                        ],
                        [
                            'email' => $company->email,
                            'phone' => $company->phone,
                            'address' => $company->address,
                        ]
                    ),
                ]);
            }

            $businessType = $company->businessType()->first();
            $productCatalog = $this->catalogForBusinessType($businessType);

            $categoryNames = $productCatalog['categories'];
            $productNames = $productCatalog['products'];

            $companyUnits = Unit::where('company_id', $company->id)->get();
            $defaultUnit = $companyUnits->firstWhere('short_code', 'pc') ?? $companyUnits->first();

            foreach ($categoryNames as $categoryName) {
                $category = Category::firstOrCreate(
                    [
                        'company_id' => $company->id,
                        'name' => $categoryName,
                    ],
                    [
                        'slug' => Str::slug($categoryName),
                        'is_active' => true,
                    ]
                );

                $productCount = $faker->numberBetween(3, 5);

                for ($i = 0; $i < $productCount; $i++) {
                    $productName = $faker->randomElement($productNames) . ' ' . $faker->randomElement(['Pro', 'Lite', 'Max', 'Plus', 'X']);
                    $hasVariants = $this->shouldCreateVariants($businessType);

                    $product = Product::firstOrCreate(
                        [
                            'company_id' => $company->id,
                            'name' => $productName,
                        ],
                        [
                            'category_id' => $category->id,
                            'description' => $faker->sentence(8),
                            'image' => null,
                            'has_variants' => $hasVariants,
                            'is_bulk' => $faker->boolean(20),
                            'is_active' => true,
                        ]
                    );

                    $variantTemplates = $this->variantTemplatesForProduct($businessType, $productName, $hasVariants);
                    if (empty($variantTemplates)) {
                        $variantTemplates = [
                            [
                                'name' => 'Default',
                                'attributes' => [],
                            ],
                        ];
                    }

                    foreach ($variantTemplates as $template) {
                        $sku = strtoupper('SKU-' . $company->id . '-' . Str::upper(Str::random(6)));
                        $barcode = strtoupper('BC-' . Str::upper(Str::random(8)));

                        $variant = ProductVariant::firstOrNew(
                            [
                                'product_id' => $product->id,
                                'name' => $template['name'],
                            ]
                        );

                        if (! $variant->exists) {
                            $variant->sku = $sku;
                            $variant->barcode = $barcode;
                        }

                        $variant->unit_id = $defaultUnit?->id;
                        $variant->cost_price = $faker->randomFloat(2, 20, 120);
                        $variant->selling_price = $faker->randomFloat(2, 80, 220);
                        $variant->reorder_level = $faker->numberBetween(5, 20);
                        $variant->attributes = $template['attributes'];
                        $variant->is_active = true;
                        $variant->save();

                        foreach ($branches as $branch) {
                            Stock::updateOrCreate(
                                [
                                    'company_id' => $company->id,
                                    'branch_id' => $branch->id,
                                    'variant_id' => $variant->id,
                                ],
                                [
                                    'quantity' => $faker->numberBetween(50, 100),
                                    'reorder_level' => $variant->reorder_level,
                                ]
                            );
                        }
                    }
                }
            }
        }
    }

    protected function shouldCreateVariants(?BusinessType $businessType): bool
    {
        $slug = $businessType?->slug ?? 'general-retail';

        return in_array($slug, ['fashion-clothing', 'electronics', 'restaurant-cafe'], true) || fake()->boolean(40);
    }

    protected function variantTemplatesForProduct(?BusinessType $businessType, string $productName, bool $hasVariants): array
    {
        $slug = $businessType?->slug ?? 'general-retail';

        if (! $hasVariants) {
            return [];
        }

        return match ($slug) {
            'fashion-clothing' => [
                ['name' => 'Size S', 'attributes' => ['size' => 'S']],
                ['name' => 'Size M', 'attributes' => ['size' => 'M']],
                ['name' => 'Size L', 'attributes' => ['size' => 'L']],
            ],
            'pharmacy' => [
                ['name' => 'Pack of 10', 'attributes' => ['pack' => '10 pcs']],
                ['name' => 'Pack of 20', 'attributes' => ['pack' => '20 pcs']],
            ],
            'restaurant-cafe' => [
                ['name' => 'Small', 'attributes' => ['size' => 'Small']],
                ['name' => 'Medium', 'attributes' => ['size' => 'Medium']],
                ['name' => 'Large', 'attributes' => ['size' => 'Large']],
            ],
            'electronics' => [
                ['name' => 'Standard', 'attributes' => []],
                ['name' => 'Premium', 'attributes' => ['model' => 'Premium']],
            ],
            default => [
                ['name' => 'Standard', 'attributes' => []],
            ],
        };
    }

    protected function catalogForBusinessType(?BusinessType $businessType): array
    {
        $slug = $businessType?->slug ?? 'general-retail';

        return match ($slug) {
            'fashion-clothing' => [
                'categories' => ['Men Wear', 'Women Wear', 'Accessories'],
                'products' => ['Shirt', 'T-Shirt', 'Jeans', 'Dress', 'Bag', 'Watch', 'Sunglass'],
            ],
            'pharmacy' => [
                'categories' => ['Medicine', 'Supplements', 'Personal Care'],
                'products' => ['Tablet', 'Syrup', 'Vitamin', 'Soap', 'Mask', 'Sanitizer'],
            ],
            'restaurant-cafe' => [
                'categories' => ['Beverages', 'Fast Food', 'Desserts'],
                'products' => ['Coffee', 'Tea', 'Burger', 'Pizza', 'Cake', 'Sandwich'],
            ],
            'electronics' => [
                'categories' => ['Mobile', 'Laptop', 'Accessories'],
                'products' => ['Phone', 'Laptop', 'Charger', 'Headphone', 'Keyboard', 'Mouse'],
            ],
            default => [
                'categories' => ['Groceries', 'Stationery', 'Household'],
                'products' => ['Basic Item', 'Daily Essential', 'Starter Pack', 'Premium Item'],
            ],
        };
    }
}
