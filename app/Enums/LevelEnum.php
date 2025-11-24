<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class LevelEnum extends Enum implements LocalizedEnum
{
    
    const STORY                  = 'Story';
    const BANNER                 = 'Banner';
    const USER                   = 'User';
    const E_LEVEL                = 'E_Level';
    const C_LEVEL                = 'C_Level';
    const COURSE                 = 'Course';
    const SUBJECT                = 'Subject';
    const UNIT                   = 'Unit';
    const SUB_UNIT               = 'Sub_Unit';
    const LESSON                 = 'Lesson';
    const FILE                   = 'File';
    const QUIZ                   = 'Quiz';
    const QUESTION               = 'Question';
    const ANSWER                 = 'Answer';
    const TEACHER                = 'Teacher';
    const COURSE_ICON            = 'Course_Icon';
    const SUBJECT_ICON           = 'Subject_Icon';
}
