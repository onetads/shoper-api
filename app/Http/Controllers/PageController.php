<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfigureShopRequest;
use App\Models\Shop;
use App\Models\ShopConfiguration;
use App\Services\DreamCommerceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(Request $request): View
    {
        $shop = $request->get('shop');

        return \view('configure', ['shop' => $shop]);
    }

    public function save(ConfigureShopRequest $request): RedirectResponse
    {
        $externalShopId = $request->get('shop_external_id');
        $websiteId = $request->get('website_id');
        $substituteProduct = (bool)$request->get('substitute_product');

        /** @var Shop $shop */
        $shop = Shop::where('shop', '=', $externalShopId)->firstOrFail();

        /** @var ShopConfiguration $shopConfiguration */
        ShopConfiguration::updateOrCreate(
            ['shop_id' => $shop->id],
            [
                'website_id' => $websiteId,
                'substitute_product' => $substituteProduct
            ]
        );

        $shopAccessToken = $shop->access_token()->access_token;
        $dreamCommerceService = new DreamCommerceService(
            $shop->shop_url,
            $shopAccessToken
        );
        $dreamCommerceService->createWebsiteIdMetaField(
            $websiteId
        );
        return redirect()->back()->with(['success' => true]);
    }
}
