<?php

namespace App\Enums;

enum QuizResultEnum: string
{
    case SUCCESS = 'success';
    case IN_PROGRESS = 'in_progress';
    case FAIL    = 'fail';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
