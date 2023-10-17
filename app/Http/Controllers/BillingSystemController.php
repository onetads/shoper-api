<?php

namespace App\Http\Controllers;

use DreamCommerce\Exception\HandlerException;
use DreamCommerce\Handler;
use Illuminate\Http\Request;

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
        } catch (HandlerException $ex) {
            if ($ex->getCode() == HandlerException::HASH_FAILED) {
                throw new \Exception('Payload hash verification failed', 0, $ex);
            } else {
                throw new \Exception('Handler initialization failed', 0, $ex);
            }
        }
    }

    public function installHandler(array $arguments)
    {
        // TODO:: implement
    }

    public function billingInstallHandler(array $arguments)
    {
        // TODO:: implement
    }


    public function upgradeHandler(array $arguments)
    {
        // TODO:: implement
    }

    public function uninstallHandler(array $arguments)
    {
        // TODO:: implement
    }

    public function billingSubscriptionHandler(array $arguments)
    {
        // TODO:: implement
    }
}
