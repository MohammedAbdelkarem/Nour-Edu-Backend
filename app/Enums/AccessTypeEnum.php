<?php

namespace App\Enums;

enum AccessTypeEnum: string
{
    case FREE          = 'free';
    case PAID          = 'paid';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
