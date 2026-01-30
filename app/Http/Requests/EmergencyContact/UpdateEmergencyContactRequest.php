<?php

namespace App\Http\Requests\EmergencyContact;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmergencyContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:150',
            'contact_number' => 'sometimes|string|max:20',
            'icon' => 'nullable|string|max:100',
            'badge' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}
