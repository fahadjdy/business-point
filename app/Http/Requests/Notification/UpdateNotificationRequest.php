<?php

namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'message' => 'sometimes|string',
            'priority' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'is_scheduled' => 'nullable|boolean',
            'scheduled_at' => 'nullable|date',
            'scheduled_at' => 'nullable|date',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048', // 2MB Max per image
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:media,id',
        ];
    }
}
