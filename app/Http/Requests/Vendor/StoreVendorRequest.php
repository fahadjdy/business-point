<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_name' => 'required|string|max:150',
            'vendor_type' => 'required|in:shop,doctor,barber',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'description' => 'nullable|string',

            // Sub-profile validation
            'profile.shop_name' => 'required_if:vendor_type,shop,barber|string|max:150',
            'profile.category' => 'required_if:vendor_type,shop|string|max:100',
            'profile.clinic_name' => 'required_if:vendor_type,doctor|string|max:150',
            'profile.specialization' => 'required_if:vendor_type,doctor|string|max:100',
            'profile.clinic_address' => 'required_if:vendor_type,doctor|string',
            'profile.services' => 'required_if:vendor_type,barber|string',
        ];
    }
}
