<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use Illuminate\Database\Seeder;

class BusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $businessTypes = [
            ['name' => 'General Retail', 'slug' => 'general-retail'],
            ['name' => 'Fashion & Clothing', 'slug' => 'fashion-clothing'],
            ['name' => 'Pharmacy', 'slug' => 'pharmacy'],
            ['name' => 'Restaurant & Cafe', 'slug' => 'restaurant-cafe'],
            ['name' => 'Electronics', 'slug' => 'electronics'],
        ];

        foreach ($businessTypes as $businessType) {
            BusinessType::firstOrCreate(
                ['slug' => $businessType['slug']],
                ['name' => $businessType['name'], 'is_active' => true]
            );
        }
    }
}
