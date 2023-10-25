<?php

namespace App\Rules;

use App\Exceptions\DreamCommerceException;
use App\Models\Shop;
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
        /** @var Shop $shop */
        $shop = Shop::where('shop', '=', $this->dataToCheck['shop'])->firstOrFail();
        try {
            $dreamCommerceService = new DreamCommerceService(
                $shop->shop_url,
                $shop->access_token()->first()->access_token,
                $shop
            );
            if (!$dreamCommerceService->checkHash((string) $value, $this->dataToCheck)){
                $fail("Zły hash");
            }
        } catch (DreamCommerceException $e) {
            $fail($e->getMessage());
        }
    }
}
