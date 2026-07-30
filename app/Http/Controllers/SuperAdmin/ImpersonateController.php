<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonateController extends Controller
{
    /**
     * Tenants (companies) লিস্ট দেখায় — কোনটাতে impersonate করবেন বেছে নেওয়ার জন্য
     */
    public function index()
    {
        // শুধুমাত্র সক্রিয় বা ট্রায়াল-এ থাকা কোম্পানিগুলো দেখানো ভালো প্র্যাকটিস
        $companies = Company::whereIn('status', ['active', 'trial'])
            ->with('owner') // পারফরম্যান্সের জন্য owner রিলেশন লোড করা
            ->latest()
            ->paginate(20);

        return view('super-admin.tenants.index', compact('companies'));
    }

    /**
     * নির্দিষ্ট company এর admin ইউজার হিসেবে লগইন করে দেখায়
     */
    public function impersonate(Company $company)
    {
        // FIX 1: নেস্টেড impersonation আটকানো হচ্ছে।
        if (Session::has('impersonator_id')) {
            return redirect()->route('impersonate.leave')
                ->with('error', 'You are already impersonating a tenant. Please return to Super Admin first, then try again.');
        }

        // FIX 2: মাল্টি-লেভার ফলব্যাক সিস্টেম (যাতে "no admin user" এরর না আসে)

        // স্ট্র্যাটেজি ১: কোম্পানির মূল মালিক (owner) কে খোঁজা (companies টেবিলের user_id কলাম থেকে)
        $tenantAdmin = $company->owner;

        // স্ট্র্যাটেজি ২: যদি owner না পাই, তবে 'Company Admin' রোল আছে এমন ইউজার খোঁজা
        if (! $tenantAdmin) {
            $tenantAdmin = User::where('company_id', $company->id)
                ->role('Company Admin') // Spatie Permission ব্যবহার করলে এই স্কোপ কাজ করবে
                ->first();
        }

        // স্ট্র্যাটেজি ৩: যদি তবুও না পাই, তবে ওই কোম্পানির যেকোনো একটি ইউজারকে ধরা (Last Resort)
        if (! $tenantAdmin) {
            $tenantAdmin = User::where('company_id', $company->id)->first();
        }

        // যদি সব স্ট্র্যাটেজি ফেইল করে, তখনই এরর দেখানো হবে
        if (! $tenantAdmin) {
            return back()->with('error', 'This company has no associated user to impersonate. Please create or assign a user to this company first.');
        }

        // FIX 3: নিষ্ক্রিয়/সাসপেন্ড করা admin কে impersonate করতে দেওয়া হচ্ছে না।
        // (আপনার User মডেলে যদি 'is_active' বা অন্য কোনো কলাম নাম থাকে, তবে 'status' এর জায়গায় সেটি বসিয়ে দিন)
        if (property_exists($tenantAdmin, 'status') && $tenantAdmin->status !== 'active') {
            return back()->with('error', 'This tenant admin account is inactive/suspended and cannot be impersonated.');
        }

        // আসল Super Admin কে মনে রাখা হচ্ছে, যাতে পরে ফিরে আসা যায়
        Session::put('impersonator_id', Auth::id());

        // টেন্যান্ট ইউজার হিসেবে লগইন করানো হচ্ছে
        Auth::login($tenantAdmin);

        return redirect()->route('company.dashboard')
            ->with('success', 'You are now viewing as ' . $tenantAdmin->name . ' (' . $company->name . ')');
    }

    /**
     * impersonation থেকে বেরিয়ে আসল super admin এ ফিরে যাওয়ার মেথড
     */
    public function leave()
    {
        $impersonatorId = Session::pull('impersonator_id');

        if (! $impersonatorId) {
            // impersonate mode-এই ছিল না
            return redirect()->route('superadmin.dashboard')
                ->with('error', 'You are not currently impersonating any tenant.');
        }

        $originalAdmin = User::find($impersonatorId);

        // FIX 4: original super admin আর না থাকলে (delete/disable) silent 403-loop এড়ানো হচ্ছে
        if (! $originalAdmin) {
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Your original admin account could not be restored. Please log in again.');
        }

        // আবার আসল সুপার অ্যাডমিন হিসেবে লগইন করানো হচ্ছে
        Auth::login($originalAdmin);

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'You have successfully returned to your Super Admin account.');
    }
}
