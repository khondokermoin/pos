<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BarcodeSetting;
use Illuminate\Http\Request;

class BarcodeSettingController extends Controller
{
    public function index()
    {
        $settings = BarcodeSetting::latest()->paginate(15);
        return view('super-admin.barcode-settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'barcode_type'       => 'required|in:CODE128,CODE39,EAN13,QR',
            'width'              => 'required|integer|min:1|max:10',
            'height'             => 'required|integer|min:20|max:200',
            'labels_per_row'     => 'required|integer|min:1|max:6',
            'show_text'          => 'boolean',
            'show_price'         => 'boolean',
            'show_product_name'  => 'boolean',
            'show_company_name'  => 'boolean',
            'is_default'         => 'boolean',
            'is_active'          => 'boolean',
        ]);

        $validated['show_text']         = $request->boolean('show_text');
        $validated['show_price']        = $request->boolean('show_price');
        $validated['show_product_name'] = $request->boolean('show_product_name');
        $validated['show_company_name'] = $request->boolean('show_company_name');
        $validated['is_default']        = $request->boolean('is_default');
        $validated['is_active']         = $request->boolean('is_active', true);

        BarcodeSetting::create($validated);

        return redirect()->route('superadmin.barcode-settings.index')
            ->with('success', 'Barcode setting created successfully.');
    }

    public function update(Request $request, BarcodeSetting $barcodeSetting)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'barcode_type'       => 'required|in:CODE128,CODE39,EAN13,QR',
            'width'              => 'required|integer|min:1|max:10',
            'height'             => 'required|integer|min:20|max:200',
            'labels_per_row'     => 'required|integer|min:1|max:6',
            'show_text'          => 'boolean',
            'show_price'         => 'boolean',
            'show_product_name'  => 'boolean',
            'show_company_name'  => 'boolean',
            'is_default'         => 'boolean',
            'is_active'          => 'boolean',
        ]);

        $validated['show_text']         = $request->boolean('show_text');
        $validated['show_price']        = $request->boolean('show_price');
        $validated['show_product_name'] = $request->boolean('show_product_name');
        $validated['show_company_name'] = $request->boolean('show_company_name');
        $validated['is_default']        = $request->boolean('is_default');
        $validated['is_active']         = $request->boolean('is_active', true);

        $barcodeSetting->update($validated);

        return redirect()->route('superadmin.barcode-settings.index')
            ->with('success', 'Barcode setting updated successfully.');
    }

    public function destroy(BarcodeSetting $barcodeSetting)
    {
        if ($barcodeSetting->is_default) {
            return back()->with('error', 'Cannot delete the default barcode setting.');
        }

        $barcodeSetting->delete();

        return back()->with('success', 'Barcode setting deleted.');
    }

    public function setDefault(BarcodeSetting $barcodeSetting)
    {
        BarcodeSetting::query()->update(['is_default' => false]);
        $barcodeSetting->update(['is_default' => true]);

        return back()->with('success', "'{$barcodeSetting->name}' set as default barcode setting.");
    }
}
