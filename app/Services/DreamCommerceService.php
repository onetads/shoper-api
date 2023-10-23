<?php

namespace App\Services;

use DreamCommerce\Client;
use DreamCommerce\Exception\ClientException;
use DreamCommerce\Exception\HandlerException;
use DreamCommerce\Exception\ResourceException;
use DreamCommerce\Handler;
use DreamCommerce\Resource\Metafield;
use DreamCommerce\Resource\MetafieldValue;
use Illuminate\Support\Facades\Log;

class DreamCommerceService
{

    protected Client $client;

    private const NAME_SPACE_FOR_ONET_ADS = 'OnetAds';
    private const NAME_FOR_META_FIELD = 'website_id';

    public function __construct(
        string $entryPoint,
        string $accessToken
    )
    {
        try {
            $this->client = new Client(
                $entryPoint,
                config('app-store.app_id'),
                config('app-store.app_secret'),
            );
        } catch (ClientException $e) {
            die('Something went wrong with the Client: ' . $e->getMessage());
        }

        $this->client->setAccessToken($accessToken);
    }

    public function createWebsiteIdMetaField(
        string $websiteId
    )
    {
        try {
            try {

                $metaField = new Metafield($this->client);
                $data = [
                    'namespace' => self::NAME_SPACE_FOR_ONET_ADS,
                    'key' => self::NAME_FOR_META_FIELD,
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

            } catch (ResourceException $ex) {
                die('Check your request: ' . $ex->getMessage());
            }
        } catch (\Exception $e) {
            Log::channel('dreamcommerce')->error($e->getMessage());

            return \response($e->getMessage(), 500);
        }

    }
}
