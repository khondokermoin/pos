<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // মিডলওয়্যার ইতিমধ্যে Super Admin চেক করছে, তাই এখানে true রিটার্ন করা নিরাপদ
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'company_id' => 'nullable|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name', // Spatie Permission এর জন্য
        ];
    }

    /**
     * Custom error messages (ঐচ্ছিক কিন্তু ভালো প্র্যাকটিস)
     */
    public function messages(): array
    {
        return [
            'roles.required' => 'At least one role must be assigned.',
            'email.unique' => 'This email address is already registered.',
        ];
    }
}