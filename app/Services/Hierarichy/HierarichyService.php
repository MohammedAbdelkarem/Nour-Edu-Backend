<?php

namespace App\Services\Hierarichy;

use App\Models\Unit;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\SubUnit;

/**
 * Class HierarichyService.
 */
class HierarichyService
{
    public function getSubject($subject_id)
    {
        $subject = Subject::findByIdOrFail($subject_id , [
            'publishedUnits',
            'publishedFiles',
            'publishedQuizzes',
            'responsibilities.teacher',
        ]);

        return $subject;
    }

    public function getUnit($unit_id)
    {
        $unit = Unit::findByIdOrFail($unit_id , [
            'publishedSubUnits',
        ]);

        return $unit;
    }

    public function getSubUnit($sub_unit_id)
    {
        $sub_unit = SubUnit::findByIdOrFail($sub_unit_id , [
            'publishedLessons.publishedFiles',
            'publishedLessons.publishedQuizzes',
            'publishedFiles',
            'publishedQuizzes',
        ]);

        return $sub_unit;
    }

    public function getUnitDetails($unit_id)
    {
        $unit = Unit::findByIdOrFail($unit_id , [
            'publishedFiles',
            'publishedQuizzes',
            'responsibilities.teacher',
        ]);

        return $unit;
    }

    public function getPurchasedContextByModel($student_id, $data, $model , $with)
    {
        return getOrPaginate(
            $model::whereHas('unlockedContexts', function($query) use ($student_id , $data) {
                $query->where('user_id', $student_id)
                ->whereHas('context', function($query) use ($data) {
                    $query->published()->filter($data);
                });
            })
            ->with($with),
            $data
        );
    }
}
