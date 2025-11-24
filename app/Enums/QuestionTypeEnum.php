<?php

namespace App\Enums;

enum QuestionTypeEnum: string
{
    case ONE_SELECT      = 'one_select';
    case MULTIPLE_SELECT = 'multiple_select';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
