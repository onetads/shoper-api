<?php

namespace App\Rules;

use App\Services\DreamCommerceService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckHashValidationRule implements ValidationRule
{
    public function __construct(public array $dataToCheck)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!DreamCommerceService::checkHash((string)$value, $this->dataToCheck)) {
            $fail('Zły hash');
        }
    }
}
