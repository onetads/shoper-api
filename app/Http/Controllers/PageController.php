<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfigureShopIndexRequest;
use App\Models\Shop;
use App\Services\OnetAdsService;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(ConfigureShopIndexRequest $request): View|Response
    {
        /** @var Shop $shop */
        $shop = Shop::where('shop', '=', $request->get('shop'))->first();
        $onetAdsService = new OnetAdsService($shop);

        $view = match ($onetAdsService->onetAdsStatus()) {
            OnetAdsService::SHOP_DOESNT_EXISTS => 'no_exists',
            OnetAdsService::SHOP_IS_INACTIVE => 'inactive',
            OnetAdsService::SHOP_IS_ACTIVE => 'active'
        };

        return \view("onetads.$view", ['shop' => $shop]);
    }
}
