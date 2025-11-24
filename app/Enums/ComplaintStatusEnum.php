<?php

namespace App\Enums;

enum ComplaintStatusEnum: string
{
    case PENDING    = 'pending';
    case PROCESSED    = 'processed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
