<?php

namespace App\Traits;

use Carbon\Carbon;

trait ProductHelper
{
    protected function getDiscountData($variant): array
    {
        if ($variant->discount_expire && Carbon::now()->lt(Carbon::parse($variant->discount_expire))) {
            return [
                "price"             => (float) $variant->price,
                "discount_price"    => (float) $variant->discount_price,
                "discount_expire"   => Carbon::parse($variant->discount_expire)->translatedFormat('Y-m-d g:i a'),
            ];
        } else
            return [
                "price"             => (float) $variant->price,
                "discount_price"    => null,
                "discount_expire"   => null,
            ];
    }
}
