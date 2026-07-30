<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'group'];

    /**
     * সহজে সেটিংস পাওয়ার জন্য Helper Method (With Cache)
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 1440, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();

            // যদি ভ্যালু JSON ফরম্যাটে থাকে (যেমন: পেমেন্ট গেটওয়ে কনফিগ), তবে ডিকোড করে অ্যারে হিসেবে রিটার্ন করবে
            if ($setting && json_decode($setting->value, true) !== null) {
                return json_decode($setting->value, true);
            }

            return $setting ? $setting->value : $default;
        });
    }

    /**
     * সহজে সেটিংস সেভ বা আপডেট করার জন্য Helper Method (With Cache Clear)
     */
    public static function set(string $key, $value, string $group = 'general')
    {
        // যদি ভ্যালু অ্যারে বা অবজেক্ট হয়, তবে JSON এ কনভার্ট করে ডাটাবেসে সেভ করবে
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        // ক্যাশ আপডেট করা (যাতে পরের বার ডাটাবেসে না গিয়ে ক্যাশ থেকে নতুন ডেটা পায়)
        Cache::put("setting_{$key}", $value, 1440);

        // গ্রুপ ক্যাশও ক্লিয়ার করে দেওয়া যাতে getByGroup() লেটেস্ট ডেটা পায়
        Cache::forget("settings_group_{$group}");

        return $setting;
    }

    /**
     * একটি নির্দিষ্ট গ্রুপের সকল সেটিংস অ্যারে হিসেবে পাওয়ার জন্য (ফর্ম পপুলেট করার জন্য উপযোগী)
     */
    public static function getByGroup(string $group): array
    {
        return Cache::remember("settings_group_{$group}", 1440, function () use ($group) {
            return self::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }

    /**
     * নির্দিষ্ট কি (key) এর ক্যাশ মুছে ফেলার জন্য (যদি ম্যানুয়ালি ক্লিয়ার করতে চান)
     */
    public static function forget(string $key): void
    {
        Cache::forget("setting_{$key}");
    }
}
