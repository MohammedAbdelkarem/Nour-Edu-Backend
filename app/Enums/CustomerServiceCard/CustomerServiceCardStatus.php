<?php

namespace App\Enums\CustomerServiceCard;

enum CustomerServiceCardStatus: string
{
    case PENDING = 'pending';
    case CLOSED = 'closed';

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
