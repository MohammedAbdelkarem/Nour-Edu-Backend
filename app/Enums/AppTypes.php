<?php

namespace App\Enums;

enum AppTypes: string
{
    //TODO:TEMPLATE
    case FIRST_APP   = 'first_app';
    case SEC_APP     = 'sec_app';
    case ALL         = 'all';   //Don't edit this

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getAppFromKey($key): string
    {
        return match ($key) {
            "eHQA2CjsllYNdNAFjmt29lIDEGOhQ4WigJ5iMrsk3DAz3n5a" => self::FIRST_APP->value,
            "f5pwsntnM4MLZAS0Euszp9lcpmR9mjCsnMIaEDlzZKCwcw8I" => self::SEC_APP->value,
        };
    }
}

