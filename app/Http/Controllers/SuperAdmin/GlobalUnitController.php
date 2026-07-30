<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GlobalUnit;
use Illuminate\Http\Request;

class GlobalUnitController extends Controller
{
    public function index()
    {
        $units = GlobalUnit::latest()->paginate(20);
        return view('super-admin.global-inventory.units', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100|unique:global_units,name',
            'short_code' => 'required|string|max:20|unique:global_units,short_code',
            'is_active'  => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        GlobalUnit::create($validated);

        return redirect()->route('superadmin.global-units.index')
            ->with('success', 'Unit of measure created successfully.');
    }

    public function update(Request $request, GlobalUnit $globalUnit)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100|unique:global_units,name,' . $globalUnit->id,
            'short_code' => 'required|string|max:20|unique:global_units,short_code,' . $globalUnit->id,
            'is_active'  => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $globalUnit->update($validated);

        return redirect()->route('superadmin.global-units.index')
            ->with('success', 'Unit of measure updated successfully.');
    }

    public function destroy(GlobalUnit $globalUnit)
    {
        $globalUnit->delete();
        return back()->with('success', 'Unit of measure deleted.');
    }
}
