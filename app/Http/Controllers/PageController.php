<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfigureShopIndexRequest;
use App\Models\Shop;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(ConfigureShopIndexRequest $request): View|Response
    {
        /** @var Shop $shop */
        $shop = Shop::where('shop', '=', $request->get('shop'))->first();

        return \view('configure', ['shop' => $shop]);
    }
}
