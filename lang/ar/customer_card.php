<?php

use App\Enums\CustomerServiceCard\CustomerServiceCardStatus;
use App\Enums\CustomerServiceCard\CustomerServiceCardTypes;

return [

    /*
    |--------------------------------------------------------------------------
    | Customer Service Card Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for customer service card for
    | various messages that we need to display to the user.
    |
    */

    CustomerServiceCardTypes::BUG->value        => 'مشكلة تقنية',
    CustomerServiceCardTypes::INQUIRY->value    => 'استفسار',
    CustomerServiceCardTypes::SUGGESTION->value => 'اقتراح',

    // CustomerServiceCardStatus::OPEN->value      => 'نشطة',
    CustomerServiceCardStatus::CLOSED->value    => 'مغلق',
    CustomerServiceCardStatus::PENDING->value   => 'بالانتظار',

];
