<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\InvoiceTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceTemplateController extends Controller
{
    public function index()
    {
        $templates = InvoiceTemplate::latest()->paginate(15);
        return view('super-admin.invoice-templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'type'         => 'required|in:pos,a4,thermal',
            'html_content' => 'nullable|string',
            'is_default'   => 'boolean',
            'is_active'    => 'boolean',
        ]);

        // Build a unique slug manually here so the boot observer doesn't
        // generate a duplicate when the record is created.
        // We append a timestamp to guarantee uniqueness even for same-name templates.
        $slug = Str::slug($validated['name']) . '-' . time();

        InvoiceTemplate::create([
            'name'         => $validated['name'],
            'slug'         => $slug,
            'type'         => $validated['type'],
            'html_content' => $validated['html_content'] ?? null,
            'is_default'   => $request->boolean('is_default'),
            'is_active'    => $request->boolean('is_active', true),
        ]);

        return redirect()->route('superadmin.invoice-templates.index')
            ->with('success', 'Invoice template created successfully.');
    }

    public function update(Request $request, InvoiceTemplate $invoiceTemplate)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'type'         => 'required|in:pos,a4,thermal',
            'html_content' => 'nullable|string',
            'is_default'   => 'boolean',
            'is_active'    => 'boolean',
        ]);

        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_active']  = $request->boolean('is_active', true);

        $invoiceTemplate->update($validated);

        return redirect()->route('superadmin.invoice-templates.index')
            ->with('success', 'Invoice template updated successfully.');
    }

    public function destroy(InvoiceTemplate $invoiceTemplate)
    {
        if ($invoiceTemplate->is_default) {
            return back()->with('error', 'Cannot delete the default template. Set another as default first.');
        }

        $invoiceTemplate->delete();

        return back()->with('success', 'Invoice template deleted.');
    }

    public function setDefault(InvoiceTemplate $invoiceTemplate)
    {
        InvoiceTemplate::query()->update(['is_default' => false]);
        $invoiceTemplate->update(['is_default' => true]);

        return back()->with('success', "'{$invoiceTemplate->name}' set as default template.");
    }

    public function preview(InvoiceTemplate $invoiceTemplate)
    {
        return view('super-admin.invoice-templates.preview', compact('invoiceTemplate'));
    }
}
