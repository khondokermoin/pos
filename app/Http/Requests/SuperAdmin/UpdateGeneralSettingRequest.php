<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateGeneralSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // শুধুমাত্র Super Admin রোল থাকলেই এই রিকোয়েস্ট প্রসেস হবে
        return Auth::check() && Auth::user()->hasRole('Super Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'app_name'     => 'required|string|max:255',
            'app_timezone' => 'required|string|timezone',
            // আপনার ফর্মে আরও যেসব ফিল্ড আছে সেগুলো এখানে যোগ করতে পারেন
            // যেমন: 'app_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors. (ঐচ্ছিক)
     */
    public function messages(): array
    {
        return [
            'app_name.required' => 'অ্যাপ্লিকেশনের নাম অবশ্যই দিতে হবে।',
            'app_timezone.required' => 'টাইমজোন নির্বাচন করা বাধ্যতামূলক।',
            'app_timezone.timezone' => 'সঠিক টাইমজোন নির্বাচন করুন।',
        ];
    }
}
