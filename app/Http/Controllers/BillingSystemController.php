<?php

namespace App\Http\Controllers;

use App\Models\AccessToken;
use App\Models\Shop;
use DreamCommerce\Client;
use DreamCommerce\Exception\HandlerException;
use DreamCommerce\Handler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingSystemController extends Controller
{
    protected Handler $handler;


    /**
     * @param Request $request
     * @return void
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $entrypoint = $request->get('shop_url');

        try {
            $this->handler = new Handler(
                $entrypoint,
                config('app-store.app_id'),
                config('app-store.app_secret'),
                config('app-store.appstore_secret')
            );

            // subscribe to particular events
            $this->handler->subscribe('install', [$this, 'installHandler']);
            $this->handler->subscribe('upgrade', [$this, 'upgradeHandler']);
            $this->handler->subscribe('billing_install', [$this, 'billingInstallHandler']);
            $this->handler->subscribe('billing_subscription', [$this, 'billingSubscriptionHandler']);
            $this->handler->subscribe('uninstall', [$this, 'uninstallHandler']);

            $this->handler->dispatch();
        } catch (HandlerException $e) {
            if ($e->getCode() == HandlerException::HASH_FAILED) {
                throw new \Exception('Payload hash verification failed', 0, $e);
            } else {
                throw new \Exception('Handler initialization failed', 0, $e);
            }
        }
    }

    /**
     * @param \ArrayObject $arguments
     * @return void
     * @throws \DreamCommerce\Exception\ClientException
     */
    public function installHandler(\ArrayObject $arguments): void
    {
        try {
            DB::beginTransaction();

            /** @var Shop $shop */
            $shop = Shop::firstOrNew([
                'shop' => $arguments['shop']
            ]);

            $shop->fill([
                'shop_url' => $arguments['shop_url'],
                'version' => $arguments['application_version'],
                'installed' => true
            ]);

            $shop->save();

            /** @var Client $client */
            $client = $arguments['client'];

            $tokenData = $client->getToken($arguments['auth_code']);

            $accessToken = AccessToken::firstOrNew([
                'shop_id' => $shop->id
            ]);

            $accessToken->fill([
                'expires_at' => now()->addSeconds($tokenData['expires_in'])->format('Y-m-d H:i:s'),
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token']
            ]);

            $accessToken->save();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

        DB::commit();
    }

    public function billingInstallHandler(\ArrayObject $arguments)
    {
        // TODO:: implement
    }


    public function upgradeHandler(\ArrayObject $arguments)
    {
        // TODO:: implement
    }

    public function uninstallHandler(\ArrayObject $arguments)
    {
        // TODO:: implement
    }

    public function billingSubscriptionHandler(\ArrayObject $arguments)
    {
        // TODO:: implement
    }

    private function getHash($payload): string
    {
        $providedHash = $payload['hash'];
        unset($payload['hash']);

        // sort params
        ksort($payload);

        $processedPayload = "";

        foreach($payload as $k => $v){
            $processedPayload .= '&'.$k.'='.$v;
        }

        $processedPayload = substr($processedPayload, 1);

        $computedHash = hash_hmac('sha512', $processedPayload, '');

        return $computedHash;
    }
}
