<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization handled by middleware / gate for Super Admin
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Basic Info ────────────────────────────────────────────────
            'name'             => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:companies,slug',
            'email'            => 'required|email|max:255|unique:companies,email',
            'contact_person'   => 'nullable|string|max:255',
            'phone'            => 'nullable|string|max:50',
            'website'          => 'nullable|url|max:255',

            // ── Address ───────────────────────────────────────────────────
            'address'          => 'nullable|string',
            'city'             => 'nullable|string|max:100',
            'country'          => 'nullable|string|max:100',
            'zip_code'         => 'nullable|string|max:20',

            // ── Domain ────────────────────────────────────────────────────
            'subdomain'        => 'nullable|string|lowercase|max:100|alpha_dash|unique:companies,subdomain',
            'custom_domain'    => 'nullable|string|max:255|unique:companies,custom_domain',

            // ── Branding ──────────────────────────────────────────────────
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'favicon'          => 'nullable|image|mimes:jpeg,png,jpg,ico,svg|max:512',
            'primary_color'    => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'],
            'secondary_color'  => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'],
            'accent_color'     => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'],

            // ── SaaS / Subscription ───────────────────────────────────────
            'company_status'   => 'required|in:active,trial,inactive,suspended',
            'plan_id'          => 'nullable|exists:plans,id',
            'trial_ends_at'    => 'nullable|date',
            'currency'         => 'nullable|string|max:10',
            'timezone'         => 'nullable|string|max:100',
            'business_type_id' => 'nullable|exists:business_types,id',

            // ── Social Links ──────────────────────────────────────────────
            'social_facebook'  => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_twitter'   => 'nullable|url|max:255',
            'social_youtube'   => 'nullable|url|max:255',

            // ── Contact Info (footer) ─────────────────────────────────────
            'contact_phone'    => 'nullable|string|max:50',
            'contact_email'    => 'nullable|email|max:255',
            'contact_address'  => 'nullable|string|max:500',

            // ── Existing Admin Assignment ─────────────────────────────────
            'user_id'          => 'nullable|exists:users,id',

            // ── New Admin Creation (only required when creating a new admin) ──
            // These are conditionally required: if admin_email is present, all three must be present.
            'admin_name'       => 'nullable|string|max:255|required_with:admin_email',
            'admin_email'      => 'nullable|email|unique:users,email|required_with:admin_name',
            'admin_password'   => 'nullable|string|min:8|confirmed|required_with:admin_name',
        ];
    }

    public function messages(): array
    {
        return [
            'subdomain.alpha_dash'        => 'Subdomain may only contain letters, numbers, dashes and underscores.',
            'email.required'              => 'Company email is required.',
            'email.unique'                => 'This email is already registered to another company.',
            'admin_name.required_with'    => 'Admin name is required when creating a new admin.',
            'admin_email.required_with'   => 'Admin email is required when creating a new admin.',
            'admin_password.required_with' => 'Admin password is required when creating a new admin.',
        ];
    }
}
