<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigureShopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'website_id' => ['required', 'string', 'max:128'],
            'shop_external_id' => ['required', 'string', 'exists:shops,shop'],
            'substitute_product' => ['sometimes', 'boolean'],
        ];
    }
}
