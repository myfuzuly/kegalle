<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:190'],
            'type'        => ['nullable', 'in:product,classified'],
            'ad_type'     => ['nullable', 'in:sale,rent,wanted,free,exchange'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'store_id'    => ['nullable', 'exists:stores,id'],
            'price'       => ['nullable', 'numeric', 'min:0'],
            'description' => ['required', 'string', 'min:20'],
            'images'      => ['nullable', 'array', 'max:6'],
            'images.*'    => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ];
    }
}
