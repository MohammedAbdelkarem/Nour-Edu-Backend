<?php

namespace App\Enums;

enum CouponTypeEnum: string
{
    case STUDENT_ONE_TIME = 'student_one_time';
    case CONTEXT_ONE_TIME = 'context_one_time';
    case CONTEXT_MANY_TIMES = 'context_many_times';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}