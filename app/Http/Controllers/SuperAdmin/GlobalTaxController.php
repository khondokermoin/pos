<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GlobalTax;
use Illuminate\Http\Request;

class GlobalTaxController extends Controller
{
    public function index()
    {
        $taxes = GlobalTax::latest()->paginate(20);
        return view('super-admin.global-inventory.taxes', compact('taxes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:global_taxes,name',
            'rate'      => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        GlobalTax::create($validated);

        return redirect()->route('superadmin.global-taxes.index')
            ->with('success', 'Tax / VAT rate created successfully.');
    }

    public function update(Request $request, GlobalTax $globalTax)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:global_taxes,name,' . $globalTax->id,
            'rate'      => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $globalTax->update($validated);

        return redirect()->route('superadmin.global-taxes.index')
            ->with('success', 'Tax / VAT rate updated successfully.');
    }

    public function destroy(GlobalTax $globalTax)
    {
        $globalTax->delete();
        return back()->with('success', 'Tax / VAT rate deleted.');
    }
}
