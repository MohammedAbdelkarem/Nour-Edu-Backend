<?php

namespace App\Enums;

enum SystemSettingsKeys: string
{
    //TODO:TEMPLATE
    case SYP_TO_DLR         = 'SP To USD';
    case BANNER_LIVE_TIME   = 'Banner live time';
    case REEL_LIVE_TIME     = 'Reel live time';
    case STEPS_REWARD_VALUE     = 'Steps Reward Value';
    case STEPS_DAILY_GOAL     = 'Steps Daily Goal';
    case STEPS_MINIMUM_BALANCE_TO_GET     = 'Steps Minimum Balance to Get';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}