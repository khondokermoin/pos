<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BusinessModule;
use App\Models\BusinessType;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessModuleController extends Controller
{
    public function index()
    {
        $modules       = BusinessModule::with('businessTypes')->latest()->paginate(20);
        $businessTypes = BusinessType::active()->get();
        return view('super-admin.business-modules.index', compact('modules', 'businessTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255|unique:business_modules,name',
            'description'      => 'nullable|string|max:500',
            'icon'             => 'nullable|string|max:100',
            'is_core'          => 'boolean',
            'is_active'        => 'boolean',
            'business_type_ids' => 'nullable|array',
            'business_type_ids.*' => 'exists:business_types,id',
        ]);

        $module = BusinessModule::create([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'icon'        => $validated['icon'] ?? null,
            'is_core'     => $request->boolean('is_core'),
            'is_active'   => $request->boolean('is_active', true),
        ]);

        // Sync business type associations
        if (!empty($validated['business_type_ids'])) {
            $module->businessTypes()->sync($validated['business_type_ids']);
        }

        return redirect()->route('superadmin.business-modules.index')
            ->with('success', 'Business module created successfully.');
    }

    public function update(Request $request, BusinessModule $businessModule)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255|unique:business_modules,name,' . $businessModule->id,
            'description'      => 'nullable|string|max:500',
            'icon'             => 'nullable|string|max:100',
            'is_core'          => 'boolean',
            'is_active'        => 'boolean',
            'business_type_ids' => 'nullable|array',
            'business_type_ids.*' => 'exists:business_types,id',
        ]);

        $businessModule->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon'        => $validated['icon'] ?? null,
            'is_core'     => $request->boolean('is_core'),
            'is_active'   => $request->boolean('is_active', true),
        ]);

        $businessModule->businessTypes()->sync($validated['business_type_ids'] ?? []);

        return redirect()->route('superadmin.business-modules.index')
            ->with('success', 'Business module updated successfully.');
    }

    public function destroy(BusinessModule $businessModule)
    {
        if ($businessModule->is_core) {
            return back()->with('error', 'Core modules cannot be deleted.');
        }

        $businessModule->delete();
        return back()->with('success', 'Business module deleted.');
    }

    /**
     * Manage module access for a specific company.
     */
    public function companyModules(Company $company)
    {
        $allModules     = BusinessModule::active()->get();
        $enabledModules = $company->modules()->pluck('business_module_id')->toArray();

        return view('super-admin.business-modules.company-modules', compact('company', 'allModules', 'enabledModules'));
    }

    /**
     * Save module access for a specific company.
     */
    public function saveCompanyModules(Request $request, Company $company)
    {
        $request->validate([
            'module_ids'   => 'nullable|array',
            'module_ids.*' => 'exists:business_modules,id',
        ]);

        $moduleIds = $request->input('module_ids', []);

        // Always include core modules
        $coreModuleIds = BusinessModule::where('is_core', true)->pluck('id')->toArray();
        $moduleIds     = array_unique(array_merge($moduleIds, $coreModuleIds));

        // Sync with is_enabled = true for all selected
        $syncData = [];
        foreach ($moduleIds as $id) {
            $syncData[$id] = ['is_enabled' => true];
        }

        $company->modules()->sync($syncData);

        return redirect()->route('superadmin.companies.index')
            ->with('success', "Module access updated for {$company->name}.");
    }
}
