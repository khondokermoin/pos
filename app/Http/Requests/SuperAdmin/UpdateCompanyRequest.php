<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Super Admin only - handled by route middleware
        return true;
    }

    public function rules(): array
    {
        $company = $this->route('company');
        $companyId = $company instanceof \App\Models\Company ? $company->getKey() : $company;

        return [
            // Basic Info
            'name'             => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:companies,slug,' . $companyId,
            'email'            => 'required|email|max:255|unique:companies,email,' . $companyId,
            'contact_person'   => 'nullable|string|max:255',
            'phone'            => 'nullable|string|max:50',
            'website'          => 'nullable|url|max:255',

            // Address
            'address'          => 'nullable|string',
            'city'             => 'nullable|string|max:100',
            'country'          => 'nullable|string|max:100',
            'zip_code'         => 'nullable|string|max:20',

            // Domain Configuration
            'subdomain'        => 'nullable|string|lowercase|alpha_dash|max:100|unique:companies,subdomain,' . $companyId,
            'custom_domain'    => 'nullable|string|max:255|unique:companies,custom_domain,' . $companyId,

            // Branding
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'favicon'          => 'nullable|image|mimes:jpeg,png,jpg,ico,svg|max:512',
            'primary_color'    => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'],
            'secondary_color'  => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'],
            'accent_color'     => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'],

            // Social Links
            'social_facebook'  => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_twitter'   => 'nullable|url|max:255',
            'social_youtube'   => 'nullable|url|max:255',

            // Contact Info (for footer)
            'contact_phone'    => 'nullable|string|max:50',
            'contact_email'    => 'nullable|email|max:255',
            'contact_address'  => 'nullable|string|max:500',

            // SaaS Settings
            'currency'         => 'nullable|string|max:10',
            'timezone'         => 'nullable|string|max:50',
            'company_status'   => 'required|in:active,inactive,suspended,trial',
            'plan_id'          => 'nullable|exists:plans,id',
            'user_id'          => 'nullable|exists:users,id',
            'business_type_id' => 'nullable|exists:business_types,id',
            'trial_ends_at'    => 'nullable|date',
            'admin_password'   => 'nullable|string|min:8',
            'settings'         => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'subdomain.alpha_dash'  => 'Subdomain may only contain letters, numbers, dashes and underscores.',
            'primary_color.regex'   => 'Primary color must be a valid hex color (e.g. #FF5722).',
            'secondary_color.regex' => 'Secondary color must be a valid hex color.',
            'accent_color.regex'    => 'Accent color must be a valid hex color.',
        ];
    }
}
