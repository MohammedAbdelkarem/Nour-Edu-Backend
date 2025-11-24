<?php

namespace App\Enums\Notifications;

enum NotificationTypes: string
{
    case AUTH = 'auth';
    case SUBSCRIPTION = 'subscription';
    case RESERVATIONS = 'reservations';
    case MEDICAL_PROFILE = 'medical_profile';
    case RATE = 'rate';
    case COMPLAINTS = 'complaints';
    case TREATMENT_REMINDER = 'treatment_reminder';
    case STEPS = 'steps';
    case WATER = 'water';
    case SLEEP = 'sleep';
    case WEIGHT = 'weight';
    case GENERAL  = 'general';
    case ARTICLES  = 'articles';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}