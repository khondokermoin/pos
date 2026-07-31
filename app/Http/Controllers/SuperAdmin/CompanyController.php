<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\Plan;
use App\Models\User;
use App\Models\BusinessType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CompanyController extends Controller
{
    /**
     * Display a listing of the companies.
     */
    public function index()
    {
        // ✅ 'owner' রিলেশনশিপ ব্যবহার করা হয়েছে (আপনার মডেল অনুযায়ী)
        $companies = Company::with(['plan', 'owner', 'businessType'])
            ->withCount(['users', 'branches'])
            ->latest()
            ->get();

        $stats = [
            'total'     => $companies->count(),
            'active'    => $companies->where('status', 'active')->count(),
            'trial'     => $companies->where('status', 'trial')->count(),
            'suspended' => $companies->where('status', 'suspended')->count(),
        ];

        return view('super-admin.companies.index', compact('companies', 'stats'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create()
    {
        $plans          = Plan::where('status', 'active')->get();
        $users          = User::all();
        $business_types = BusinessType::where('is_active', true)->get();

        return view('super-admin.companies.create', compact('plans', 'users', 'business_types'));
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(\App\Http\Requests\SuperAdmin\StoreCompanyRequest $request)
    {
        $validated = $request->validated();
        $uploadedPaths = [];

        if ($request->hasFile('logo')) {
            $uploadedPaths['logo'] = $request->file('logo')->store('companies/logos', 'public');
            $validated['logo'] = $uploadedPaths['logo'];
        }

        if ($request->hasFile('favicon')) {
            $uploadedPaths['favicon'] = $request->file('favicon')->store('companies/favicons', 'public');
            $validated['favicon'] = $uploadedPaths['favicon'];
        }

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        }

        $themeSettings = array_filter([
            'primary_color'   => $request->input('primary_color', '#3B82F6'),
            'secondary_color' => $request->input('secondary_color', '#1E40AF'),
            'accent_color'    => $request->input('accent_color', '#F59E0B'),
        ], fn($value) => filled($value));

        $socialLinks = array_filter([
            'facebook'  => $request->input('social_facebook'),
            'instagram' => $request->input('social_instagram'),
            'twitter'   => $request->input('social_twitter'),
            'youtube'   => $request->input('social_youtube'),
        ], fn($value) => filled($value));

        $contactInfo = array_filter([
            'phone'   => $request->input('contact_phone'),
            'email'   => $request->input('contact_email'),
            'address' => $request->input('contact_address'),
        ], fn($value) => filled($value));

        // Use manually set trial_ends_at if provided, otherwise auto-calculate from plan
        $trialEndsAt = null;
        if ($request->filled('trial_ends_at')) {
            $trialEndsAt = $request->input('trial_ends_at');
        } elseif (($validated['company_status'] ?? null) === 'trial') {
            $plan = Plan::find($request->input('plan_id'));
            $trialDays = $plan ? ($plan->trial_days ?? 14) : 14;
            $trialEndsAt = now()->addDays($trialDays);
        }

        DB::beginTransaction();
        try {
            $company = Company::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'contact_person' => $request->input('contact_person'),
                'website' => $request->input('website'),
                'address' => $validated['address'] ?? null,
                'city' => $request->input('city'),
                'country' => $request->input('country'),
                'zip_code' => $request->input('zip_code'),
                'logo' => $validated['logo'] ?? null,
                'favicon' => $validated['favicon'] ?? null,
                'theme_settings' => $themeSettings,
                'social_links' => $socialLinks,
                'contact_info' => $contactInfo,
                'subdomain' => strtolower(trim((string) ($validated['subdomain'] ?? ''))),
                'custom_domain' => $validated['custom_domain'] ?? null,
                'currency' => $request->input('currency', 'BDT'),
                'timezone' => $request->input('timezone', 'Asia/Dhaka'),
                'settings' => $request->input('settings', []),
                'status' => $validated['company_status'],
                'trial_ends_at' => $trialEndsAt,
                'plan_id' => $request->input('plan_id'),
                'business_type_id' => $request->input('business_type_id'),
            ]);

            // Handle Company Admin Assignment
            // Priority: 1. New admin creation, 2. Existing user selection
            $user = null;

            $adminName = $request->input('admin_name');
            $adminEmail = $request->input('admin_email');
            $adminPassword = $request->input('admin_password');
            $existingUserId = $request->input('user_id');

            // Create new admin if provided
            if (! empty($adminName) && ! empty($adminEmail) && ! empty($adminPassword)) {
                $user = User::create([
                    'name' => $adminName,
                    'email' => $adminEmail,
                    'password' => \Illuminate\Support\Facades\Hash::make($adminPassword),
                    'company_id' => $company->id,
                ]);
            }
            // Use existing user if selected and no new admin is being created
            elseif (! empty($existingUserId)) {
                $user = User::find($existingUserId);
                if ($user) {
                    // Update company_id if needed
                    $user->update(['company_id' => $company->id]);
                }
            }

            // Link the user as the company owner and assign role
            if ($user) {
                // Set user_id on the company record so owner() relationship works
                $company->update(['user_id' => $user->id]);

                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('Company Admin');
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'message'  => 'Company and admin user created successfully!',
                    'redirect' => route('superadmin.companies.index'),
                ]);
            }

            return redirect()->route('superadmin.companies.index')
                ->with('success', 'Company and admin user created successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();

            foreach ($uploadedPaths as $path) {
                if (! empty($path) && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Something went wrong while creating the company. Please try again.',
                    'error'   => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->withInput()->with('error', 'Something went wrong while creating the company. Please try again.');
        }
    }

    /**
     * Display the specified company.
     */
    public function show($id)
    {
        $company = Company::with(['plan', 'owner', 'businessType'])
            ->withCount(['users', 'branches'])
            ->findOrFail($id);

        // Real usage stats — directly queried without global scopes
        $stats = [
            'products'   => \App\Models\Product::where('company_id', $company->id)->count(),
            'categories' => \App\Models\Category::where('company_id', $company->id)->count(),
            'sales'      => \App\Models\Sale::where('company_id', $company->id)->count(),
        ];

        return view('super-admin.companies.show', compact('company', 'stats'));
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit($id)
    {
        $company        = Company::findOrFail($id);
        $plans          = Plan::where('status', 'active')->get();
        $users          = User::all();
        $business_types = BusinessType::where('is_active', true)->get();

        return view('super-admin.companies.edit', compact('company', 'plans', 'users', 'business_types'));
    }

    /**
     * Update the specified company in storage.
     * Handles: logo, favicon, theme_settings, social_links, contact_info
     */
    public function update(UpdateCompanyRequest $request, $id)
    {
        $company = Company::findOrFail($id);
        $validated = $request->validated();
        $uploaded = [];

        if ($request->hasFile('logo')) {
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $uploaded['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($company->favicon && Storage::disk('public')->exists($company->favicon)) {
                Storage::disk('public')->delete($company->favicon);
            }
            $uploaded['favicon'] = $request->file('favicon')->store('companies/favicons', 'public');
        }

        $slug = $validated['slug'] ?? null;
        if (empty($slug)) {
            $slug = Str::slug($validated['name']) . '-' . Str::random(5);
        }

        $existingTheme = is_array($company->theme_settings) ? $company->theme_settings : [];
        $themeSettings = array_merge($existingTheme, array_filter([
            'primary_color' => $request->filled('primary_color') ? $request->input('primary_color') : ($existingTheme['primary_color'] ?? '#2563eb'),
            'secondary_color' => $request->filled('secondary_color') ? $request->input('secondary_color') : ($existingTheme['secondary_color'] ?? null),
            'accent_color' => $request->filled('accent_color') ? $request->input('accent_color') : ($existingTheme['accent_color'] ?? null),
        ], fn($value) => filled($value)));

        $socialLinks = array_filter([
            'facebook' => $request->input('social_facebook', $company->social_links['facebook'] ?? null),
            'instagram' => $request->input('social_instagram', $company->social_links['instagram'] ?? null),
            'twitter' => $request->input('social_twitter', $company->social_links['twitter'] ?? null),
            'youtube' => $request->input('social_youtube', $company->social_links['youtube'] ?? null),
        ], fn($value) => filled($value));

        $contactInfo = array_filter([
            'phone' => $request->input('contact_phone', $company->contact_info['phone'] ?? null),
            'email' => $request->input('contact_email', $company->contact_info['email'] ?? null),
            'address' => $request->input('contact_address', $company->contact_info['address'] ?? null),
        ], fn($value) => filled($value));

        $trialEndsAt = $request->filled('trial_ends_at')
            ? $request->trial_ends_at
            : $company->trial_ends_at;

        if (($validated['company_status'] ?? null) === 'trial' && empty($trialEndsAt)) {
            $plan = Plan::find($validated['plan_id'] ?? $company->plan_id);
            $trialDays = $plan ? ($plan->trial_days ?? 14) : 14;
            $trialEndsAt = now()->addDays($trialDays);
        }

        $oldUserId = $company->user_id;
        $newUserId = $validated['user_id'];

        $assignedAdmin = $newUserId ? User::find($newUserId) : null;
        if ($request->filled('admin_password') && $assignedAdmin) {
            $assignedAdmin->update([
                'password' => Hash::make($request->input('admin_password')),
            ]);
        }

        $company->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'email' => $validated['email'],
            'contact_person' => $validated['contact_person'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'website' => $validated['website'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
            'zip_code' => $validated['zip_code'] ?? null,
            'subdomain' => strtolower(trim((string) ($validated['subdomain'] ?? ''))),
            'custom_domain' => $validated['custom_domain'] ?? null,
            'logo' => $uploaded['logo'] ?? $company->logo,
            'favicon' => $uploaded['favicon'] ?? $company->favicon,
            'theme_settings' => $themeSettings,
            'social_links' => $socialLinks,
            'contact_info' => $contactInfo,
            'currency' => $validated['currency'] ?? 'BDT',
            'timezone' => $validated['timezone'] ?? 'Asia/Dhaka',
            'status' => $validated['company_status'],
            'plan_id' => $validated['plan_id'],
            'user_id' => $newUserId,
            'business_type_id' => $validated['business_type_id'],
            'trial_ends_at' => $trialEndsAt,
            'settings' => $request->has('settings') ? $request->settings : $company->settings,
        ]);

        // ── Role Reassignment ─────────────────────────────────────────
        if ($oldUserId !== $newUserId) {
            if (! empty($oldUserId)) {
                $oldUser = User::find($oldUserId);
                if ($oldUser && method_exists($oldUser, 'removeRole')) {
                    $oldUser->removeRole('Company Admin');
                }
            }
            $newUser = User::find($newUserId);
            if ($newUser && method_exists($newUser, 'assignRole')) {
                $newUser->assignRole('Company Admin');
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message'  => 'Company updated successfully!',
                'redirect' => route('superadmin.companies.index'),
            ]);
        }

        return redirect()->route('superadmin.companies.index')
            ->with('success', 'Company updated successfully!');
    }

    /**
     * Impersonate the company admin for the selected tenant.
     */
    public function impersonate(Company $company)
    {
        if (! Auth::check() || ! Auth::user()->hasRole('Super Admin')) {
            abort(403);
        }

        if (Session::has('impersonated_by') || Session::has('impersonator_id')) {
            return redirect()->route('impersonate.leave')
                ->with('error', 'You are already impersonating a tenant. Please return to Super Admin first, then try again.');
        }

        $tenantAdmin = User::query()
            ->where('company_id', $company->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Company Admin');
            })
            ->first();

        if (! $tenantAdmin) {
            return back()->with('error', 'No admin user found for this company.');
        }

        $superAdminId = Auth::id();
        Session::put('impersonated_by', $superAdminId);
        Session::put('impersonator_id', $superAdminId);
        Auth::login($tenantAdmin);

        return redirect($this->tenantDashboardUrl($company))
            ->with('success', 'You are now viewing as ' . $tenantAdmin->name . ' (' . $company->name . ')');
    }

    public function leaveImpersonation(Request $request)
    {
        $impersonatorId = Session::pull('impersonated_by');

        if (! $impersonatorId) {
            $impersonatorId = Session::pull('impersonator_id');
        }

        if (! $impersonatorId) {
            return redirect()->route('superadmin.dashboard')
                ->with('error', 'You are not currently impersonating any tenant.');
        }

        $originalAdmin = User::find($impersonatorId);

        Session::forget('impersonated_by');
        Session::forget('impersonator_id');

        if (! $originalAdmin) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Your original Super Admin account could not be restored. Please log in again.');
        }

        Auth::logout();
        Auth::login($originalAdmin);
        $request->session()->regenerate();

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'You have successfully returned to your Super Admin account.');
    }

    protected function tenantDashboardUrl(Company $company): string
    {
        $scheme = parse_url(config('app.url'), PHP_URL_SCHEME) ?: 'http';
        $hostname = $company->custom_domain ?: null;

        if (! $hostname && ! empty($company->subdomain)) {
            $defaultDomain = config('app.domain', parse_url(config('app.url'), PHP_URL_HOST) ?: request()->getHost());
            $hostname = $company->subdomain . '.' . $defaultDomain;
        }

        if (! $hostname) {
            return route('company.dashboard');
        }

        $host = preg_replace('#^https?://#', '', trim($hostname));
        return $scheme . '://' . trim($host, '/') . '/company/dashboard';
    }

    /**
     * Remove the specified company from storage.
     */
    public function destroy(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        if ($company->logo && Storage::disk('public')->exists($company->logo)) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message'  => 'Company deleted successfully!',
                'redirect' => route('superadmin.companies.index')
            ]);
        }

        return redirect()->route('superadmin.companies.index')
            ->with('success', 'Company deleted successfully!');
    }
}
