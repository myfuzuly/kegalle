<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:190',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:190',
            'whatsapp' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:120',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_logo' => 'nullable|boolean',
            'remove_banner' => 'nullable|boolean',
            'whatsapp_same' => ['nullable'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ];
    }
}
