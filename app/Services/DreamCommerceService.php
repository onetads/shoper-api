<?php

namespace App\Services;

use App\Exceptions\DreamCommerceException;
use App\Models\AccessToken;
use App\Models\Shop;
use DreamCommerce\Client;
use DreamCommerce\Exception\ClientException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DreamCommerceService
{

    protected Client $client;
    private const ACCESS_TOKEN_RENEW_DIFF_IN_DAYS = 1;
    private const ALGORITHM_NAME_TO_HASH = 'sha512';
    private const ONET_API_URL = 'https://csr.onet.pl/1551662/tags';
    private static array $itemsToRemoveFromShopDomain = ['https://', 'http://', '/'];
    private const TPL_CODE_IF_NOT_EXISTS_OR_INACTIVE = 'lps/RMN';
    private static array $statusesInactive = ['inactive', 'deactivated'];
    private const STATUS_ACTIVE = 'active';

    private const SHOP_ERROR = -1;
    private const SHOP_DOESNT_EXISTS = 0;
    private const SHOP_IS_INACTIVE = 1;
    private const SHOP_IS_ACTIVE = 2;

    public function __construct(
        string $entryPoint,
        string $accessToken,
        protected ?Shop $shop = null
    )
    {
        try {
            $this->client = new Client(
                $entryPoint,
                config('app-store.app_id'),
                config('app-store.app_secret'),
            );
        } catch (ClientException $e) {
            Log::channel('dreamcommerce')->error($e->getMessage());
            throw new DreamCommerceException($e->getMessage());
        }

        $this->client->setAccessToken($accessToken);

        if (Carbon::parse($shop->access_token()->first()->expires_at)->diffInDays(Carbon::now()) < self::ACCESS_TOKEN_RENEW_DIFF_IN_DAYS) {
            $this->refreshToken($shop);
        }

    }

    public function refreshToken(Shop $shop): void
    {
        $refreshToken = $shop->access_token()->first()->refresh_token;
        $tokenData = $this->client->getToken($refreshToken);

        $accessToken = AccessToken::firstOrNew([
            'shop_id' => $shop->id
        ]);

        $accessToken->fill([
            'expires_at' => now()->addSeconds($tokenData['expires_in'])->format('Y-m-d H:i:s'),
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token']
        ]);

        $accessToken->save();
    }

    public static function checkHash(string $hash, array $dataToCheck): bool
    {
        ksort($dataToCheck);
        $hashArray = [];
        foreach ($dataToCheck as $key=>$value) {
            $hashArray[] = "$key=$value";
        }
        $hashFromData = implode('&', $hashArray);
        $hashFromData = hash_hmac(self::ALGORITHM_NAME_TO_HASH, $hashFromData, config('app-store.appstore_secret'));

        return hash_equals($hash, $hashFromData);
    }

    public function onetAdsStatus(): int
    {
        $domain = Str::replace(self::$itemsToRemoveFromShopDomain, '', $this->shop->shop_url);
        $formattedDomain = self::formatDomainForOnetAds($domain);
        $response = Http::get(self::ONET_API_URL, [
            'domain' => $domain,
            'site' => $formattedDomain
        ])->body();

        $status = self::checkOnetAdsStatus($response);

        return ($status);
    }

    private static function formatDomainForOnetAds(string $domain): string
    {
        return preg_replace('/[^\w-]+/', '_', $domain);
    }

    private static function checkOnetAdsStatus(string $data): int
    {
        $data = json_decode($data);

        if (self::shopDoesntExists($data)) {
            return self::SHOP_DOESNT_EXISTS;
        }

        if (self::shopIsInactive($data)) {
            return self::SHOP_IS_INACTIVE;
        }
        if (self::shopIsActive($data)) {
            return self::SHOP_IS_ACTIVE;
        }

        return self::SHOP_ERROR;
    }

    private static function shopDoesntExists(\stdClass $data): bool
    {
        if (!$data->tags || !$data->tags->page_context) {
           return true;
        }

        foreach ($data->tags->page_context as $item) {
            if ($item->data->tplCode === self::TPL_CODE_IF_NOT_EXISTS_OR_INACTIVE) {
                return true;
            }
        }

        return false;
    }

    private static function shopIsInactive(\stdClass $data): bool
    {
        foreach ($data->tags->page_context as $item) {
            if ($item->data->tplCode === self::TPL_CODE_IF_NOT_EXISTS_OR_INACTIVE
                && in_array($item->data->fields->status, self::$statusesInactive)
            ) {
                return true;
            }
        }
        return false;
    }

    private static function shopIsActive(\stdClass $data): bool
    {
        foreach ($data->tags->page_context as $item) {
            if ($item->data->tplCode === self::TPL_CODE_IF_NOT_EXISTS_OR_INACTIVE
                && $item->data->tplCode->status === self::STATUS_ACTIVE) {
                return true;
            }
        }
        return false;
    }
}
