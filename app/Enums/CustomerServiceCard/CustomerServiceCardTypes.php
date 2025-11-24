<?php

namespace App\Enums\CustomerServiceCard;

enum CustomerServiceCardTypes: string
{
    case INQUIRY = 'inquiry';
    case BUG = 'bug';
    case SUGGESTION = 'suggestion';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function transValues(): array
    {
        $values = self::cases();
        foreach ($values as $value) {
            $data[] = [
                "key"       => $value->value,
                "value"     => __("customer_card.{$value->value}"),
            ];
        }
        return $data;
    }
}
