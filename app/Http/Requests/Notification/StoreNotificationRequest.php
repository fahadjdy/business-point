<?php

namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'is_scheduled' => 'nullable|boolean',
            'scheduled_at' => 'nullable|date',
            'sort_order' => 'nullable|integer',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048', // 2MB Max per image
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:media,id',
        ];
    }
}
