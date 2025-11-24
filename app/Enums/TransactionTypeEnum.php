<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case COUPON_CHARGE = 'coupon_charge';
    case GATEWAY_CHARGE = 'gateway_charge'; // not used yet
    case COUPON_PURCHASE = 'coupon_purchase';
    case DIRECT_PURCHASE = 'direct_purchase';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}