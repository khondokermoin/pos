<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting; // ⚠️ এই লাইনটি যোগ করা জরুরি (এটি না থাকায় এরর হচ্ছিল)

class SettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key'   => 'app_name',
                'value' => 'Advanced Cloud POS',
                'group' => 'general'
            ],
            [
                'key'   => 'app_timezone',
                'value' => 'Asia/Dhaka',
                'group' => 'general'
            ],
            [
                'key'   => 'stripe_enabled',
                'value' => '0',
                'group' => 'payment'
            ],
            [
                'key'   => 'mail_mailer',
                'value' => 'smtp',
                'group' => 'email'
            ],
            [
                'key'   => 'mail_host',
                'value' => 'smtp.mailtrap.io',
                'group' => 'email'
            ],
        ];

        foreach ($settings as $setting) {
            // updateOrCreate ব্যবহার করলে ডেটা আগে থেকে থাকলে আপডেট হবে, না থাকলে নতুন তৈরি হবে
            Setting::updateOrCreate(
                ['key' => $setting['key']], // যে কলামটি চেক করে ম্যাচ খুঁজবে
                $setting                    // যে ডেটা ইনসার্ট বা আপডেট হবে
            );
        }

        $this->command->info('✅ Global settings seeded successfully!');
    }
}
