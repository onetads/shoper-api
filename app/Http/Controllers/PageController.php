<?php

namespace App\Http\Controllers;

use App\Exceptions\DreamCommerceException;
use App\Http\Requests\ConfigureShopRequest;
use App\Models\Shop;
use App\Models\ShopConfiguration;
use App\Services\DreamCommerceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(Request $request): View|Response
    {
        /** @var Shop $shop */
        $shop = Shop::where('shop', '=', $request->get('shop'))->first();
        $shopAccessToken = $shop->access_token()->access_token;
        try {
            $dreamCommerceService = new DreamCommerceService($shop->shop_url, $shopAccessToken);
            $metaFields = $dreamCommerceService->getMetaFields();
            $websiteId = $metaFields->where('key', '=', DreamCommerceService::NAME_FOR_META_FIELD_WEBSITE_ID) ?? "";
            $substituteProduct = $metaFields->where('key', '=', DreamCommerceService::NAME_FOR_META_FIELD_SUBSTITUTE_PRODUCT);
        } catch (DreamCommerceException $e) {
            return \response($e->getMessage(), 500);
        }

        return \view('configure', ['shop' => $shop, 'website_id' => $websiteId, 'substitute_product' => $substituteProduct]);
    }

    public function save(ConfigureShopRequest $request): Response | RedirectResponse
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
        try {
            $dreamCommerceService = new DreamCommerceService(
                $shop->shop_url,
                $shopAccessToken
            );
            $dreamCommerceService->createMetaFields(
                $websiteId,
                $substituteProduct
            );
        } catch (DreamCommerceException $exception) {
            return \response($exception->getMessage(), 500);
        }

        return redirect()->back()->with(['success' => true]);
    }
}
