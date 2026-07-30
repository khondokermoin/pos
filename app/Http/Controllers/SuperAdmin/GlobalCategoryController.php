<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GlobalCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GlobalCategoryController extends Controller
{
    public function index()
    {
        $categories = GlobalCategory::latest()->paginate(20);
        return view('super-admin.global-inventory.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:global_categories,name',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        $validated['slug']      = Str::slug($validated['name']) . '-' . time();
        $validated['is_active'] = $request->boolean('is_active', true);

        GlobalCategory::create($validated);

        return redirect()->route('superadmin.global-categories.index')
            ->with('success', 'Global category created successfully.');
    }

    public function update(Request $request, GlobalCategory $globalCategory)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:global_categories,name,' . $globalCategory->id,
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $globalCategory->update($validated);

        return redirect()->route('superadmin.global-categories.index')
            ->with('success', 'Global category updated successfully.');
    }

    public function destroy(GlobalCategory $globalCategory)
    {
        $globalCategory->delete();
        return back()->with('success', 'Global category deleted.');
    }
}
