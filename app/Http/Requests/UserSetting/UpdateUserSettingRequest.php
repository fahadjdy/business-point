<?php

namespace App\Http\Requests\UserSetting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'settings' => 'required|array',
            'settings.*' => 'nullable'
        ];
    }
}
