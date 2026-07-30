<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        // শুধুমাত্র Super Admin এই রিকোয়েস্ট করতে পারবে
        return Auth::check() && Auth::user()->hasRole('Super Admin');
    }

    public function rules(): array
    {
        return [
            'group' => 'required|string|in:general,payment,email',
            'app_name' => 'nullable|string|max:255',
            'app_timezone' => 'nullable|string|timezone',
            'stripe_enabled' => 'nullable|boolean',
            'mail_mailer' => 'nullable|string',
            'mail_host' => 'nullable|string',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            // প্রয়োজন অনুযায়ী আরও ফিল্ড যোগ করতে পারেন
        ];
    }
}
