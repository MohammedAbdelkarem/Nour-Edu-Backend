<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * @method static static Male()
 * @method static static Female()
 */
final class GenderEnum extends Enum implements LocalizedEnum
{
    const MALE      = 'male';
    const FEMALE    = 'female';
}