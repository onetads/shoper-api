<?php

namespace App\Services;

use App\Exceptions\DreamCommerceException;
use App\Models\AccessToken;
use App\Models\Shop;
use DreamCommerce\Client;
use DreamCommerce\Exception\ClientException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class DreamCommerceService
{

    protected Client $client;
    private const ACCESS_TOKEN_RENEW_DIFF_IN_DAYS = 1;
    private const ALGORITHM_NAME_TO_HASH = 'sha512';

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
}
