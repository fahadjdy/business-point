<?php

namespace App\Http\Requests\ContactBook;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'designation' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'type' => 'required|in:person,business,service',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'image' => 'nullable|image|max:2048',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|exists:tags,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Contact name is required.',
            'phone.required' => 'Phone number is required.',
            'email.email' => 'Please provide a valid email address.',
            'type.required' => 'Contact type is required.',
            'type.in' => 'Contact type must be person, business, or service.',
            'tag_ids.array' => 'Tags must be provided as an array.',
            'tag_ids.*.exists' => 'One or more selected tags do not exist.',
        ];
    }
}
