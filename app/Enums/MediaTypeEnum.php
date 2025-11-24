<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class MediaTypeEnum extends Enum implements LocalizedEnum
{
    const IMAGE = 'image';
    const VIDEO = 'video';
    const FILE  = 'file';
}
