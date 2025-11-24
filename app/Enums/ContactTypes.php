<?php

namespace App\Enums;

enum ContactTypes: string
{
    case EMAIL = 'email';
    case PHONE_NUMBER = 'phone-number';
    case LINK = 'link';
    case FACEBOOK_LINK = 'facebook';
    case INSTAGRAM_LINK = 'instagram';
    case X_LINK = 'x';
    case LINKEDIN_LINK = 'linkedIn';
    case TELEGRAM_LINK = 'telegram';
    case WHATSAPP_LINK = 'whatsApp';
    case YOUTUBE_LINK = 'youtube';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
