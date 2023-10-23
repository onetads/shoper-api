<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(Shop::all()->count() === 0 ){
            Shop::create([
                'shop' => '3c36e0ece75ac2a74bc63d6dd17ec17b05a8a590', //hardcoded from test shop
                'version' => '1',
                'installed' => true,
                'shop_url' => 'https://devshop-950027.shoparena.pl/'
            ]);
        }
    }
}
