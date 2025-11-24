<?php

namespace App\Enums;

enum PublishStatusEnum: string
{
    case PUBLISHED = 'published';
    case DRAFT     = 'draft';
    case ARCHIVED  = 'archived';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
