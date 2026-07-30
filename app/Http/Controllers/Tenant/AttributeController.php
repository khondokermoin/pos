<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttributeController extends Controller
{
    public function index()
    {
        // ✅ FIX: Scoped to company_id (was missing before)
        $attributes = Attribute::where('company_id', Auth::user()->company_id)
            ->with('values')
            ->latest()
            ->get();

        return view('company.settings.attributes.index', compact('attributes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'values' => 'nullable|string',
        ]);

        // ✅ FIX: Proper company_id assignment (was broken before)
        $attribute = Attribute::create([
            'company_id' => Auth::user()->company_id,
            'name'       => $request->input('name'),
        ]);

        $values = array_filter(array_map('trim', preg_split('/[\n,]+/', $request->input('values', ''))));

        foreach ($values as $value) {
            $attribute->values()->create(['value' => $value]);
        }

        // ✅ FIX: Correct route name (was 'tenant.attributes.index')
        return redirect()->route('company.settings.attributes.index')->with('success', 'Attribute created successfully.');
    }

    public function update(Request $request, Attribute $attribute)
    {
        // Security: ensure attribute belongs to this company
        if ($attribute->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'   => 'required|string|max:255',
            'values' => 'nullable|string',
        ]);

        $attribute->update(['name' => $request->input('name')]);

        $attribute->values()->delete();

        $values = array_filter(array_map('trim', preg_split('/[\n,]+/', $request->input('values', ''))));

        foreach ($values as $value) {
            $attribute->values()->create(['value' => $value]);
        }

        // ✅ FIX: Correct route name (was 'tenant.attributes.index')
        return redirect()->route('company.settings.attributes.index')->with('success', 'Attribute updated successfully.');
    }

    public function destroy(Attribute $attribute)
    {
        // Security: ensure attribute belongs to this company
        if ($attribute->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $attribute->delete();

        // ✅ FIX: Correct route name (was 'tenant.attributes.index')
        return redirect()->route('company.settings.attributes.index')->with('success', 'Attribute deleted successfully.');
    }
}
