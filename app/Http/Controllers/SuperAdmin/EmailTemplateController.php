<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::latest()->paginate(15);
        return view('super-admin.email-templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'subject'   => 'required|string|max:255',
            'body'      => 'required|string',
            'variables' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Parse comma-separated variables into array
        $variables = [];
        if (!empty($validated['variables'])) {
            $variables = array_map('trim', explode(',', $validated['variables']));
            $variables = array_filter($variables);
        }

        EmailTemplate::create([
            'name'      => $validated['name'],
            'slug'      => Str::slug($validated['name']) . '-' . time(),
            'subject'   => $validated['subject'],
            'body'      => $validated['body'],
            'variables' => array_values($variables),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('superadmin.email-templates.index')
            ->with('success', 'Email template created successfully.');
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'subject'   => 'required|string|max:255',
            'body'      => 'required|string',
            'variables' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $variables = [];
        if (!empty($validated['variables'])) {
            $variables = array_map('trim', explode(',', $validated['variables']));
            $variables = array_filter($variables);
        }

        $emailTemplate->update([
            'name'      => $validated['name'],
            'subject'   => $validated['subject'],
            'body'      => $validated['body'],
            'variables' => array_values($variables),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('superadmin.email-templates.index')
            ->with('success', 'Email template updated successfully.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();
        return back()->with('success', 'Email template deleted.');
    }

    public function preview(EmailTemplate $emailTemplate)
    {
        // Build sample data from variables list
        $sampleData = [];
        foreach ($emailTemplate->variables ?? [] as $var) {
            $sampleData[$var] = '[' . strtoupper($var) . ']';
        }

        $renderedBody    = $emailTemplate->render($sampleData);
        $renderedSubject = $emailTemplate->renderSubject($sampleData);

        return view('super-admin.email-templates.preview', compact('emailTemplate', 'renderedBody', 'renderedSubject'));
    }
}
