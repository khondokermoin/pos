<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AddonController extends Controller
{
    /**
     * Display a listing of installed addons.
     */
    public function index()
    {
        $addons = Addon::latest()->paginate(20);
        return view('super-admin.addons.index', compact('addons'));
    }

    /**
     * Store a newly created addon.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'version'     => 'nullable|string|max:50',
            'price'       => 'nullable|numeric|min:0',
            'is_active'   => 'boolean',
        ]);

        $validated['slug']         = Str::slug($validated['name']) . '-' . time();
        $validated['is_installed'] = true;
        $validated['is_active']    = $request->boolean('is_active', true);

        Addon::create($validated);

        return redirect()->route('superadmin.addons.index')
            ->with('success', 'Addon added successfully.');
    }

    /**
     * Update the specified addon.
     */
    public function update(Request $request, Addon $addon)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'version'     => 'nullable|string|max:50',
            'price'       => 'nullable|numeric|min:0',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $addon->update($validated);

        return redirect()->route('superadmin.addons.index')
            ->with('success', 'Addon updated successfully.');
    }

    /**
     * Remove the specified addon.
     */
    public function destroy(Addon $addon)
    {
        $addon->delete();
        return redirect()->route('superadmin.addons.index')
            ->with('success', 'Addon removed.');
    }
}
