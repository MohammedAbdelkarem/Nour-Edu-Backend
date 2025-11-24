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

    CustomerServiceCardTypes::BUG->value        => 'Technical issue',
    CustomerServiceCardTypes::INQUIRY->value    => 'Inquiry',
    CustomerServiceCardTypes::SUGGESTION->value => 'Suggestion',

    // CustomerServiceCardStatus::OPEN->value      => 'Open',
    CustomerServiceCardStatus::CLOSED->value    => 'Closed',
    CustomerServiceCardStatus::PENDING->value   => 'Pending',

];
