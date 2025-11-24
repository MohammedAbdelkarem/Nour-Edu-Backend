<?php

use App\Models\File;
use App\Models\Quiz;
use App\Models\Unit;
use App\Models\User;
use App\Models\Story;
use App\Models\Answer;
use App\Models\Banner;
use App\Models\CLevel;
use App\Models\Course;
use App\Models\ELevel;
use App\Models\Lesson;
use App\Models\Replay;
use App\Models\Comment;
use App\Models\Subject;
use App\Models\SubUnit;
use Nette\Utils\Random;
use App\Enums\LevelEnum;
use App\Models\Question;
use App\Models\LessonRate;
use App\Models\QuizResult;
use App\Enums\MediaTypeEnum;
use App\Constants\ModelPaths;
use App\Enums\QuizResultEnum;
use App\Models\UnlockedContext;
use App\Enums\CommentStatusEnum;
use App\Constants\MediaCollection;
use App\Models\Download;
use Illuminate\Support\Facades\Config;
use App\Services\System\SystemSettingService;

// if (!function_exists('generateEmail')) {
//     function generateEmail()
//     {
//         do {
//             $year = date('Y');
//             $month = date('m');
//             $randomNumber = random_int(00, 99);
//             $email =  $year . $month . $randomNumber . '@friendApp.com';
//         } while (User::where('email', $email)->exists());

//         return $email;
//     }
// }

if (!function_exists('generatePassword')) {
    function generatePassword()
    {
        $year = date('Y');
        $month = date('m');
        $randomNumber = random_int(00, 99);
        $password = $year . $month . $randomNumber . Random::generate(15);
        return $password;
    }
}

if (!function_exists('customizePaginationData')) {
    function customizePaginationData($data)
    {
        $paginationData = [
            'total'          => $data['total'] ?? null,
            'per_page'       => $data['per_page'] ?? null,
            'first_page_url' => $data['first_page_url'] ?? null,
            'prev_page_url'  => $data['prev_page_url'] ?? null,
            'current_page'   => $data['current_page'] ?? null,
            'next_page_url'  => $data['next_page_url'],
            'last_page_url'  => $data['last_page_url'] ?? null,
            // 'from'           => $data['from'] ?? null,
            // 'last_page'      => $data['last_page'] ?? null,
            // 'links'          => $data['links'],
            // 'path'           => $data['path'],
            // 'to'             => $data['to'] ?? null,
        ];

        return $paginationData;
    }
}

if (!function_exists('selectRandomElement')) {
    function selectRandomElement($values, $weights)
    {

        $weightedValues = array_combine($values, $weights);
        $rand = mt_rand(1, (int) array_sum($weightedValues));

        foreach ($weightedValues as $value => $weight) {
            $rand -= $weight;
            if ($rand <= 0) {
                return $value;
            }
        }
    }
}

if (!function_exists('generateRandomNumber')) {
    function generateRandomNumber(int $numberOfDigits): string
    {
        // Ensure that the first digit is not 0 to prevent octal interpretation
        $firstDigit = mt_rand(1, 9);
        $number = (string)$firstDigit;

        // Generate the remaining digits
        for ($i = 1; $i < $numberOfDigits; $i++) {
            $number .= mt_rand(0, 9);
        }

        return $number;
    }
}

if (!function_exists('prepareTranslatableData')) {
    function prepareTranslatableData(array $validatedData): array
    {
        $translatableData = [];
        $supportedLocales = Config::get('app.available_locales', []);

        foreach ($validatedData as $key => $value) {
            if (strpos($key, '_') !== false) {
                [$field, $locale] = explode('_', $key, 2);

                if (in_array($locale, $supportedLocales)) {
                    $translatableData[$field][$locale] = $value;
                } else {
                    $translatableData[$key] = $value;
                }
            } else {
                $translatableData[$key] =  $value;
            }
        }
        return $translatableData;
    }
}

if (!function_exists('decodeStringToArray')) {
    function decodeStringToArray(string $string): array
    {
        $array = [];
        if (is_string($string)) {
            $array = json_decode(str_replace("'", '"', $string), true);
            if (!is_array($array)) $array = [];
        }
        return $array;
    }
}

if (!function_exists('dlrToSyp')) {
    function dlrToSyp(float $price, $dlrPrice = null): float
    {
        return $price * ($dlrPrice ?? (new SystemSettingService)->index()[0]["value"]);
    }
}

if (!function_exists('sypToDlr')) {
    function sypToDlr(float $price, $dlrPrice = null): float
    {
        return $price / ($dlrPrice ?? (new SystemSettingService)->index()[0]["value"]);
    }
}

if (!function_exists('getMediaType')) {
    function getMediaType($mime_type)
    {
        if ((strpos($mime_type, 'image') !== false)) {
            return MediaTypeEnum::IMAGE;
        } elseif ((strpos($mime_type, 'video') !== false)) {
            return MediaTypeEnum::VIDEO;
        } else {
            return MediaTypeEnum::FILE;
        }
    }
}

if (!function_exists('getOrPaginate')) {
    function getOrPaginate($items, $data)
    {
        $items = (isset($data['per_page']))
            ? $items->paginate($data['per_page'])
            : $items->get();

        return $items;
    }
}


if (!function_exists('mediaCollectionByContxt')) {
    function mediaCollectionByContxt($model_path)
    {
        $data = [
            LevelEnum::STORY          => MediaCollection::STORY_COLLECTION,
            LevelEnum::BANNER         => MediaCollection::BANNER_COLLECTION,
            LevelEnum::USER         => MediaCollection::USER_COLLECTION,
            LevelEnum::E_LEVEL         => MediaCollection::E_LEVEL_COLLECTION,
            LevelEnum::C_LEVEL         => MediaCollection::C_LEVEL_COLLECTION,
            LevelEnum::COURSE         => MediaCollection::COURSE_COLLECTION,
            LevelEnum::SUBJECT         => MediaCollection::SUBJECT_COLLECTION,
            LevelEnum::COURSE_ICON         => MediaCollection::COURSE_ICON_COLLECTION,
            LevelEnum::SUBJECT_ICON         => MediaCollection::SUBJECT_ICON_COLLECTION,
            LevelEnum::UNIT         => MediaCollection::UNIT_COLLECTION,
            LevelEnum::SUB_UNIT         => MediaCollection::SUB_UNIT_COLLECTION,
            LevelEnum::LESSON         => MediaCollection::LESSON_COLLECTION,
            LevelEnum::FILE         => MediaCollection::FILE_COLLECTION,
            LevelEnum::QUIZ         => MediaCollection::QUIZ_COLLECTION,
            LevelEnum::QUESTION         => MediaCollection::QUESTION_COLLECTION,
            LevelEnum::ANSWER         => MediaCollection::ANSWER_COLLECTION,
            // LevelEnum::USER        => MediaCollection::USER_COLLECTION,
        ];

        return $data[$model_path] ?? null;
    }
}


if (!function_exists('getModel')) {
    function getModel($model_path)
    {
        $data = [
            LevelEnum::STORY                => Story::class,
            LevelEnum::BANNER               => Banner::class,
            LevelEnum::USER               => User::class,
            LevelEnum::E_LEVEL               => ELevel::class,
            LevelEnum::C_LEVEL               => CLevel::class,
            LevelEnum::COURSE               => Course::class,
            LevelEnum::SUBJECT               => Subject::class,
            LevelEnum::COURSE_ICON               => Course::class,
            LevelEnum::SUBJECT_ICON               => Subject::class,
            LevelEnum::UNIT               => Unit::class,
            LevelEnum::SUB_UNIT               => SubUnit::class,
            LevelEnum::LESSON               => Lesson::class,
            LevelEnum::FILE               => File::class,
            LevelEnum::QUIZ               => Quiz::class,
            LevelEnum::QUESTION               => Question::class,
            LevelEnum::ANSWER               => Answer::class,
        ];

        return $data[$model_path] ?? null;
    }
}


if (!function_exists('getModelName')) {
    function getModelName($model_path)
    {
        $data = [
            ModelPaths::Story          => LevelEnum::STORY,
            ModelPaths::Banner         => LevelEnum::BANNER,
            ModelPaths::User         => LevelEnum::USER,
            ModelPaths::ELevel         => LevelEnum::E_LEVEL,
            ModelPaths::CLevel         => LevelEnum::C_LEVEL,
            ModelPaths::Course         => LevelEnum::COURSE,
            ModelPaths::Subject         => LevelEnum::SUBJECT,
            ModelPaths::Unit         => LevelEnum::UNIT,
            ModelPaths::SubUnit         => LevelEnum::SUB_UNIT,
            ModelPaths::Lesson         => LevelEnum::LESSON,
            ModelPaths::File         => LevelEnum::FILE,
            ModelPaths::Quiz         => LevelEnum::QUIZ,
            ModelPaths::Question         => LevelEnum::QUESTION,
            ModelPaths::Answer         => LevelEnum::ANSWER,
            ModelPaths::Teacher         => LevelEnum::TEACHER,
        ];

        return $data[$model_path] ?? null;
    }
}

if (!function_exists('getModelByPath')) {
    function getModelByPath($model_path)
    {
        $data = [
            ModelPaths::Story          => Story::class,
            ModelPaths::Banner         => Banner::class,
            ModelPaths::User         => User::class,
            ModelPaths::ELevel         => ELevel::class,
            ModelPaths::CLevel         => CLevel::class,
            ModelPaths::Course         => Course::class,
            ModelPaths::Subject         => Subject::class,
            ModelPaths::Unit         => Unit::class,
            ModelPaths::SubUnit         => SubUnit::class,
            ModelPaths::Lesson         => Lesson::class,
            ModelPaths::File         => File::class,
            ModelPaths::Quiz         => Quiz::class,
            ModelPaths::Question         => Question::class,
            ModelPaths::Answer         => Answer::class,
        ];

        return $data[$model_path] ?? null;
    }
}

if (!function_exists('maskString')) {
    function maskString($string, $visibleCharsLength = 5, $maskChar = '*')
    {
        $visiblePart = substr($string, 0, $visibleCharsLength);
        $maskedPart = str_repeat($maskChar, strlen($string) - $visibleCharsLength);
        return "$visiblePart$maskedPart";
    }
}

if (!function_exists('student_c_level_id')) {
    function student_c_level_id()
    {
        return auth()->user()->c_level_id;
    }
}

if (!function_exists('student_e_level_id')) {
    function student_e_level_id()
    {
        return auth()->user()->e_level_id;
    }
}

if (!function_exists('watched')) {
    function watched($lesson)
    {
        return $lesson->viewers()->where('student_id', auth()->id())->exists();
    }
}

if (!function_exists('generateUniqueCoupon')) {
    function generateUniqueCoupon(int $length = 4): string
    {
        do {
            $coupon = generateRandomCoupon($length);
        } while (\App\Models\Coupon::where('coupon', $coupon)->exists());

        return $coupon;
    }
}

if (!function_exists('generateRandomCoupon')) {
    function generateRandomCoupon(int $length = 10): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $coupon = '';
        
        for ($i = 0; $i < $length; $i++) {
            $coupon .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $coupon;
    }
}

if (!function_exists('is_purchased')) {
    function is_purchased($context_id , $context_type , $student_id)
    {
        $model = getModel($context_type);
        $context = $model::find($context_id);
        
        return $context->unlockedContexts()->where('user_id', $student_id)->exists();
    }
}

if (!function_exists('is_saved')) {
    function is_saved($context_id , $context_type , $student_id)
    {
        $model = getModel($context_type);
        $context = $model::find($context_id);
        
        return $context->savedByStudents()->where('student_id', $student_id)->exists();
    }
}

if (!function_exists('is_solved')) {
    function is_solved($quiz_id , $student_id , $getting = false)
    {
        $query = QuizResult::where('quiz_id', $quiz_id)
                ->where('student_id', $student_id);

        if($getting)
            return $query->first();

        return $query->where('result', '!=', QuizResultEnum::IN_PROGRESS->value)->exists();
    }
}

if (!function_exists('is_rated')) {
    function is_rated($lesson_id)
    {
        return LessonRate::where('lesson_id', $lesson_id)->where('student_id', auth()->id())->exists();
    }
}

if (!function_exists('is_commented')) {
    function is_commented($lesson_id)
    {
        return Comment::where('lesson_id', $lesson_id)
            ->where('user_id', auth()->id())
            ->where('status', CommentStatusEnum::EXIST->value)
            ->exists();
    }
}

if (!function_exists('is_replayed')) {
    function is_replayed($comment_id)
    {
        return Replay::where('comment_id', $comment_id)
            ->where('status', CommentStatusEnum::EXIST->value)
            ->exists();
    }
}

if (!function_exists('duration')) {
    function duration($context , $published = false)
    {
        
        return (int)($published 
            ? $context->publishedLessons()->sum('duration') 
            : $context->lessons()->sum('duration'));
    }
}

if (!function_exists('is_downloaded')) {
    function is_downloaded($lesson)
    {
        return Download::where('lesson_id', $lesson->id)
            ->where('user_id', auth()->id())
            ->exists();
    }
}

if (!function_exists('isFromFirstsInSubUnit')) {
    function isFromFirstsInSubUnit($lesson)
    {
        $firstLessonsArray = Lesson::where('sub_unit_id' , $lesson->sub_unit_id)
            ->published()
            ->orderBy('priority' , 'asc')
            ->limit(2)
            ->pluck('id')
            ->toArray();

        return in_array($lesson->id , $firstLessonsArray);
    }
}