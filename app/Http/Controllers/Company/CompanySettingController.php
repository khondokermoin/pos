<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanySettingController extends Controller
{
    /**
     * Show the company profile settings page.
     */
    public function profile()
    {
        $company = Auth::user()->company;
        return view('company.settings.profile', compact('company'));
    }

    /**
     * Update the company profile.
     */
    public function updateProfile(Request $request)
    {
        $company = Auth::user()->company;

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city'    => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'address', 'city', 'country']);

        if ($request->hasFile('logo')) {
            // ✅ Delete old logo if it exists
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            // ✅ Store in consistent path: companies/logos/ (same as SuperAdmin CompanyController)
            $data['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        $company->update($data);

        return redirect()->route('company.settings.profile')
            ->with('success', 'Company profile updated successfully.');
    }

    /**
     * Show the invoice settings page.
     */
    public function invoice()
    {
        $company = Auth::user()->company;
        return view('company.settings.invoice', compact('company'));
    }

    /**
     * Update invoice settings.
     */
    public function updateInvoice(Request $request)
    {
        $company = Auth::user()->company;

        $request->validate([
            'invoice_prefix'  => 'nullable|string|max:10',
            'invoice_footer'  => 'nullable|string|max:500',
            'show_logo'       => 'nullable|boolean',
            'currency_symbol' => 'nullable|string|max:5',
        ]);

        $company->update([
            'invoice_prefix'  => $request->invoice_prefix,
            'invoice_footer'  => $request->invoice_footer,
            'show_logo'       => $request->boolean('show_logo'),
            'currency_symbol' => $request->currency_symbol ?? '৳',
        ]);

        return redirect()->route('company.settings.invoice')
            ->with('success', 'Invoice settings updated successfully.');
    }
}
