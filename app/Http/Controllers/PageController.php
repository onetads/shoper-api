<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallShopRequest;
use App\Models\Shop;
use App\Models\ShopConfiguration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(Request $request): View
    {
        $shop = $request->get('shop');

        return \view('install', ['shop' => $shop]);
    }

    public function save(InstallShopRequest $request): RedirectResponse
    {
        $externalShopId = $request->get('shop_external_id');
        $websiteId = $request->get('website_id');
        $substituteProduct = (bool)$request->get('substitute_product');

        $shopId = Shop::where('shop', '=', $externalShopId)->firstOrFail()->id;
        /** @var ShopConfiguration $shopConfiguration */
        ShopConfiguration::updateOrCreate(
            ['shop_id' => $shopId],
            [
                'website_id' => $websiteId,
                'substitute_product' => $substituteProduct
            ]
        );

        return redirect()->back()->with(['success' => true]);
    }
}
