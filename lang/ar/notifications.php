<?php

use App\Constants\NotificationMessages;
use App\Enums\Notifications\NotificationTypes;

return [

    /*
    |--------------------------------------------------------------------------
    | App notifications messages Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during App for various
    | notifications messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    //Enum Keys
    NotificationTypes::AUTH->value           => "اشعارات المصادقة",

    //
    "Suspend" => "حظر",
    "Unblock" => "Unblock",

    //Messages
    //Account
    NotificationMessages::LOGIN_TITLE   => "عملية تسجيل دخول جديدة",
    NotificationMessages::LOGIN_BODY    => "تمت عملية تسجيل دخول جديدة لحسابك من جهاز: :device من: :location",
    NotificationMessages::BAN_TITLE     => "لقد تم تقييد حسابك",
    NotificationMessages::BAN_BODY      => "حسابك محظور من استخدام بعض ميزات التطبيق حتى تاريخ: :bannedUntil بسبب: :reason",
    NotificationMessages::UNBAN_TITLE   => "لقد تم الغاء تقييد حسابك",
    NotificationMessages::UNBAN_BODY    => "بإمكانك الآن الاستفادة من جميع ميزات التطبيق ,لقد تم الغاء القيود على حسابك",

    //Customer Service Card
    NotificationMessages::CUSTOMER_SERVICE_CARD_DELETE_TITLE    => "لقد تم حذف بطاقة خدمة العملاء خاصتك",
    NotificationMessages::CUSTOMER_SERVICE_CARD_DELETE_BODY     => "تم حذف بطاقة خدمة العملاء خاصتك بالعنوان التالي: ':name'",
    NotificationMessages::CUSTOMER_SERVICE_CARD_CLOSE_TITLE     => "لقد تم إغلاق خدمة العملاء خاصتك",
    NotificationMessages::CUSTOMER_SERVICE_CARD_CLOSE_BODY      => "تم إغلاق بطاقة خدمة العملاء خاصتك بالعنوان التالي: ':name'",

    // Auth Messages
    NotificationMessages::REGISTER_TITLE       => "تسجيل حساب جديد",
    NotificationMessages::REGISTER_BODY        => "مرحًبا بك في تطبيقنا الطبي! تم إنشاء حسابك بنجاح 🎉",

    NotificationMessages::WELCOME_BACK_TITLE   => "تسجيل دخول",
    NotificationMessages::WELCOME_BACK_BODY    => "أهلاً بعودتك، :name! نتمنى لك يوماً صحياً 😊",

    NotificationMessages::DEVICE_LOGIN_TITLE   => "دخول من جهاز جديد",
    NotificationMessages::DEVICE_LOGIN_BODY    => "تم تسجيل دخول جديد إلى حسابك",

];
