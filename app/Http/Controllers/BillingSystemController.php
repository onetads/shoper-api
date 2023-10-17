<?php

namespace App\Http\Controllers;

use App\Models\AccessToken;
use App\Models\Shop;
use DreamCommerce\Client;
use DreamCommerce\Exception\HandlerException;
use DreamCommerce\Handler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingSystemController extends Controller
{
    protected Handler $handler;


    /**
     * @param Request $request
     * @return void
     * @throws \Exception
     */
    public function index(Request $request): Response
    {
        $entrypoint = $request->get('shop_url');

        try {
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
        } catch (\Exception $e) {
            Log::channel('dreamcommerce')->error($e->getMessage());

            return \response($e->getMessage(), 500);
        }


        return \response('success');
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

    /**
     * @param \ArrayObject $arguments
     * @return void
     * @throws \Exception
     */
    public function billingInstallHandler(\ArrayObject $arguments): void
    {
        /** @var Shop $shop */
        $shop = Shop::query()
            ->where('shop', $arguments['shop'])
            ->firstOrFail();

        $shop->billing()->create();
    }


    /**
     * @param \ArrayObject $arguments
     * @return void
     */
    public function upgradeHandler(\ArrayObject $arguments): void
    {
        /** @var Shop $shop */
        $shop = Shop::query()
            ->where('shop', $arguments['shop'])
            ->firstOrFail();

        $shop->update(['version' => $arguments['application_version']]);
    }

    public function uninstallHandler(\ArrayObject $arguments): void
    {
        /** @var Shop $shop */
        $shop = Shop::query()
            ->where('shop', $arguments['shop'])
            ->firstOrFail();

        $shop->update(['installed' => false]);

        $shop->access_token()->delete();
    }

    /**
     * @param \ArrayObject $arguments
     * @return void
     */
    public function billingSubscriptionHandler(\ArrayObject $arguments): void
    {
        /** @var Shop $shop */
        $shop = Shop::query()
            ->where('shop', $arguments['shop'])
            ->firstOrFail();

        $expiresAt = Carbon::createFromTimestamp(strtotime($arguments['subscription_end_time']))
            ->format('Y-m-d H:i:s');

        $shop->subscription()->create(['expires_at' => $expiresAt]);
    }
}
