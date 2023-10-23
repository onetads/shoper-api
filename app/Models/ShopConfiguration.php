<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'website_id',
        'substitute_product',
    ];
}
