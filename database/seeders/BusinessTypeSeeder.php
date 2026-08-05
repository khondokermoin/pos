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
            ['name' => 'Grocery & Supermarket', 'slug' => 'grocery-supermarket'],
            ['name' => 'Mini Mart / Convenience Store', 'slug' => 'mini-mart-convenience-store'],
            ['name' => 'Department Store', 'slug' => 'department-store'],
            ['name' => 'Wholesale & Distribution', 'slug' => 'wholesale-distribution'],
            ['name' => 'Fashion & Clothing', 'slug' => 'fashion-clothing'],
            ['name' => 'Footwear & Shoes', 'slug' => 'footwear-shoes'],
            ['name' => 'Jewelry & Ornaments', 'slug' => 'jewelry-ornaments'],
            ['name' => 'Cosmetics & Perfume', 'slug' => 'cosmetics-perfume'],
            ['name' => 'Pharmacy', 'slug' => 'pharmacy'],
            ['name' => 'Restaurant & Cafe', 'slug' => 'restaurant-cafe'],
            ['name' => 'Bakery & Sweets', 'slug' => 'bakery-sweets'],
            ['name' => 'Electronics', 'slug' => 'electronics'],
            ['name' => 'Mobile & Accessories', 'slug' => 'mobile-accessories'],
            ['name' => 'Computer & IT Store', 'slug' => 'computer-it-store'],
            ['name' => 'Hardware & Tools', 'slug' => 'hardware-tools'],
            ['name' => 'Construction Materials', 'slug' => 'construction-materials'],
            ['name' => 'Furniture & Home Decor', 'slug' => 'furniture-home-decor'],
            ['name' => 'Stationery & Books', 'slug' => 'stationery-books'],
            ['name' => 'Toy Store', 'slug' => 'toy-store'],
            ['name' => 'Sports & Outdoor', 'slug' => 'sports-outdoor'],
            ['name' => 'Salon & Beauty Parlor', 'slug' => 'salon-beauty-parlor'],
            ['name' => 'Spa & Wellness', 'slug' => 'spa-wellness'],
            ['name' => 'Gym & Fitness Center', 'slug' => 'gym-fitness-center'],
            ['name' => 'Auto Parts & Accessories', 'slug' => 'auto-parts-accessories'],
            ['name' => 'Auto Service & Garage', 'slug' => 'auto-service-garage'],
            ['name' => 'Agriculture & Farming', 'slug' => 'agriculture-farming'],
            ['name' => 'Poultry & Fisheries', 'slug' => 'poultry-fisheries'],
            ['name' => 'Pet Shop & Supplies', 'slug' => 'pet-shop-supplies'],
            ['name' => 'Florist & Nursery', 'slug' => 'florist-nursery'],
            ['name' => 'Hotel & Hospitality', 'slug' => 'hotel-hospitality'],
            ['name' => 'Laundry & Dry Cleaning', 'slug' => 'laundry-dry-cleaning'],
            ['name' => 'Printing & Photocopy', 'slug' => 'printing-photocopy'],
            ['name' => 'Optical & Eyewear', 'slug' => 'optical-eyewear'],
            ['name' => 'Gift Shop', 'slug' => 'gift-shop'],
            ['name' => 'Liquor Store', 'slug' => 'liquor-store'],
            ['name' => 'Courier & Logistics', 'slug' => 'courier-logistics'],
            ['name' => 'Real Estate', 'slug' => 'real-estate'],
        ];

        foreach ($businessTypes as $businessType) {
            BusinessType::firstOrCreate(
                ['slug' => $businessType['slug']],
                ['name' => $businessType['name'], 'is_active' => true]
            );
        }
    }
}
