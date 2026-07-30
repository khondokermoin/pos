<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GlobalAttribute;
use Illuminate\Http\Request;

class GlobalAttributeController extends Controller
{
    public function index()
    {
        $attributes = GlobalAttribute::latest()->paginate(20);
        return view('super-admin.global-inventory.attributes', compact('attributes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:global_attributes,name',
            'values'    => 'required|string',
            'is_active' => 'boolean',
        ]);

        // Parse comma-separated values into array
        $values = array_values(array_filter(array_map('trim', explode(',', $validated['values']))));

        GlobalAttribute::create([
            'name'      => $validated['name'],
            'values'    => $values,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('superadmin.global-attributes.index')
            ->with('success', 'Global attribute created successfully.');
    }

    public function update(Request $request, GlobalAttribute $globalAttribute)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:global_attributes,name,' . $globalAttribute->id,
            'values'    => 'required|string',
            'is_active' => 'boolean',
        ]);

        $values = array_values(array_filter(array_map('trim', explode(',', $validated['values']))));

        $globalAttribute->update([
            'name'      => $validated['name'],
            'values'    => $values,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('superadmin.global-attributes.index')
            ->with('success', 'Global attribute updated successfully.');
    }

    public function destroy(GlobalAttribute $globalAttribute)
    {
        $globalAttribute->delete();
        return back()->with('success', 'Global attribute deleted.');
    }
}
