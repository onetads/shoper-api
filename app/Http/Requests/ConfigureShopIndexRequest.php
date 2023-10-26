<?php

namespace App\Http\Requests;

use App\Rules\CheckHashValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ConfigureShopIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //from docs: https://developers.shoper.pl/developers/appstore/shop-integration/iframes
        return [
            'shop' => ['required', 'string', 'exists:shops,shop'],
            'timestamp' => ['sometimes'],
            'place' => ['sometimes', 'string'],
            'hash' => ['sometimes', 'string', new CheckHashValidationRule($this->only(['place', 'shop', 'timestamp']))]
        ];
    }
}
