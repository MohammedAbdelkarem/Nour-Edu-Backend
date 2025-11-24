<?php

namespace App\Enums;

enum CommentStatusEnum: string
{
    case EXIST   = 'exist';
    case DELETED_BY_TEACHER = 'deleted_by_teacher';
    case DELETED_BY_STUDENT = 'deleted_by_student';
    case DELETED_BY_ADMIN = 'deleted_by_admin';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
