<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Display Settings Dashboard (Fixes the missing index route error)
     */
    public function index()
    {
        // যখন কেউ /super-admin/settings এ আসবে, তখন তাকে জেনারেল সেটিংসে রিডাইরেক্ট করবে
        return redirect()->route('superadmin.settings.general');
    }

    /**
     * Display General Settings form
     */
    public function general()
    {
        $settings = Setting::getByGroup('general');
        return view('super-admin.settings.general', compact('settings'));
    }

    /**
     * Display Payment Settings form
     */
    public function payment()
    {
        $settings = Setting::getByGroup('payment');
        return view('super-admin.settings.payment', compact('settings'));
    }

    /**
     * Display Email Settings form
     */
    public function email()
    {
        $settings = Setting::getByGroup('email');
        return view('super-admin.settings.email', compact('settings'));
    }

    /**
     * Update Settings (Unified method for all groups: general, payment, email)
     */
    public function update(Request $request)
    {
        // ১. সিকিউরিটি: গ্রুপ ভ্যালিডেশন
        $request->validate([
            'group' => 'required|string|in:general,payment,email',
        ]);

        $group = $request->input('group');

        // ২. গ্রুপ অনুযায়ী অতিরিক্ত ভ্যালিডেশন (Payment এর জন্য)
        if ($group === 'payment') {
            $request->validate([
                'default_currency' => 'required|in:BDT,USD,EUR',
                'currency_symbol_position' => 'required|in:before,after',
                'sslcommerz_environment' => 'nullable|in:sandbox,live',
            ]);
        }

        // ৩. সিস্টেম ফিল্ড বাদে বাকি সব ডেটা নেওয়া
        $data = $request->except(['_token', '_method', 'group']);

        // ৪. যেসব ফিল্ড অ্যারে/JSON হিসেবে সেভ হওয়া উচিত (আপনার ব্লেড ফাইল অনুযায়ী আপডেট করা)
        $jsonFields = ['supported_currencies']; 

        try {
            foreach ($data as $key => $value) {

                // 🛡️ প্রোডাকশন সিকিউরিটি: পাসওয়ার্ড বা সিক্রেট কি ফিল্ড ফাঁকা থাকলে আপডেট করবে না
                if (str_contains(strtolower($key), 'password') || str_contains(strtolower($key), 'secret') || str_contains(strtolower($key), 'app_secret')) {
                    if (empty($value) || $value === '••••••••') {
                        continue; // ফাঁকা থাকলে স্কিপ করে পরের ফিল্ডে যাবে
                    }
                }

                // HTML চেকবক্সের ডিফল্ট আচরণ ঠিক করা 
                // (ব্লেডে hidden input '0' এবং checkbox '1' থাকায় এটি এখন '0' বা '1' আসবে, 'on' আসবে না)
                if ($value === 'on') {
                    $value = '1';
                }

                // JSON ফিল্ড হ্যান্ডলিং
                if (in_array($key, $jsonFields) && is_string($value)) {
                    $decoded = json_decode($value, true);
                    $value = $decoded !== null ? $decoded : $value;
                }

                // মডেল হেল্পারের মাধ্যমে সেভ করা
                Setting::set($key, $value, $group);
            }

            return back()->with('success', '✅ Settings updated successfully!');

        } catch (\Exception $e) {
            Log::error('Setting Update Failed: ' . $e->getMessage(), [
                'group' => $group,
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            return back()->with('error', '❌ Failed to update settings. Please check the logs.');
        }
    }
}