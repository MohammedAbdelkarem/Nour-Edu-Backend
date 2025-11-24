<?php

namespace App\Enums;

enum AppTypeEnum: string
{
    case STUDENT    = 'student';
    case TEACHER    = 'teacher';
    case PARENT    = 'parent';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
