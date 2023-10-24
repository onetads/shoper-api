<?php

namespace App\Services;

use App\Exceptions\DreamCommerceException;
use App\Models\AccessToken;
use App\Models\Shop;
use DreamCommerce\Client;
use DreamCommerce\Exception\ClientException;
use DreamCommerce\Exception\HandlerException;
use DreamCommerce\Exception\ResourceException;
use DreamCommerce\Handler;
use DreamCommerce\Resource\Metafield;
use DreamCommerce\Resource\MetafieldValue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DreamCommerceService
{

    protected Client $client;
    private const ACCESS_TOKEN_RENEW_DIFF_IN_DAYS = 1;
    private const NAME_SPACE_FOR_ONET_ADS = 'OnetAds';
    public const NAME_FOR_META_FIELD_WEBSITE_ID = 'website_id';
    public const NAME_FOR_META_FIELD_SUBSTITUTE_PRODUCT = 'substitute_product';


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

    public function createMetaFields(string $websiteId, bool $substituteProduct): void
    {
        try {
            $metaField = new Metafield($this->client);
            $data = [
                'namespace' => self::NAME_SPACE_FOR_ONET_ADS,
                'key' => self::NAME_FOR_META_FIELD_WEBSITE_ID,
                'type' => Metafield::TYPE_STRING,
            ];
            $metaFileId = $metaField->post($data);

            $metaFieldValue = new MetafieldValue($this->client);
            $data = [
                'metafield_id' => $metaFileId,
                'object_id' => '1',
                'value' => $websiteId
            ];

            $metaFieldValue->post($data);

            $metaField = new Metafield($this->client);
            $data = [
                'namespace' => self::NAME_SPACE_FOR_ONET_ADS,
                'key' => self::NAME_FOR_META_FIELD_SUBSTITUTE_PRODUCT,
                'type' => Metafield::TYPE_INT,
            ];
            $metaFileId = $metaField->post($data);

            $metaFieldValue = new MetafieldValue($this->client);
            $data = [
                'metafield_id' => $metaFileId,
                'object_id' => '1',
                'value' => $substituteProduct ? 1 : 0
            ];

            $metaFieldValue->post($data);
        } catch (\Exception $e) {
            Log::channel('dreamcommerce')->error($e->getMessage());
            throw new DreamCommerceException($e->getMessage());
        }
    }

    public function getMetaFields(): Collection
    {
        $namespace = self::NAME_SPACE_FOR_ONET_ADS;

        $metaFields = (new Metafield($this->client))->filters(['namespace' => $namespace])->get()->getArrayCopy();
        $metaFieldsData = collect($metaFields)->mapWithKeys(function ($metaField) {
            return [$metaField['key'] => $metaField['metafield_id']];
        });

        $metaFieldValues = (new MetafieldValue($this->client))->filters(['metafield_id' => $metaFieldsData])->get('system', 'all');
        $metaFieldsValuesCollection = collect($metaFieldValues)->get('list');

        $dataToReturn = [];
        foreach ($metaFieldsData as $key => $value) {
            $matchingItem = collect($metaFieldsValuesCollection)->first(function ($item) use ($value) {
                return $item['metafield_id'] == $value;
            });

            if ($matchingItem) {
                $dataToReturn[$key] = $matchingItem['value'];
            }
        }

        return collect($dataToReturn);
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
}
