<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Company;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // প্রথম কোম্পানিটি খুঁজে বের করা (যাতে ক্যাটাগরিগুলো সঠিক টেন্যান্টের অধীনে থাকে)
        $company = Company::first();

        if (!$company) {
            $this->command->warn('কোনো কোম্পানি পাওয়া যায়নি! দয়া করে আগে CompanySeeder রান করুন।');
            return;
        }

        // সাধারণ POS/Inventory এর জন্য কিছু ডিফল্ট ক্যাটাগরি
        $defaultCategories = [
            'Electronics',
            'Groceries & Food',
            'Clothing & Apparel',
            'Home & Garden',
            'Health & Beauty',
            'Automotive',
            'Sports & Outdoors',
            'Office Supplies',
            'Toys & Games',
            'Books & Media'
        ];

        // ক্যাটাগরিগুলো ডাটাবেসে ইনসার্ট করা
        foreach ($defaultCategories as $categoryName) {
            Category::create([
                'company_id' => $company->id,
                'name'       => $categoryName,
            ]);
        }

        $this->command->info('✅ Categories seeded successfully for Company ID: ' . $company->id);
    }
}