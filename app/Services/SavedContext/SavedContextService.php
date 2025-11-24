<?php

namespace App\Services\SavedContext;

use App\Models\Lesson;
use App\Models\Question;
use App\Models\SavedContext;

/**
 * Class SavedContextService.
 */
class SavedContextService
{
    public function toggle($data)
    {
        $model = getModel($data['context_type']);

        $context = $model::findByIdOrFail($data['context_id']);

        $alreadySaved = $context->savedByStudents()->where('student_id', auth()->id())->exists();

        if($alreadySaved)
            $context->savedByStudents()->where('student_id', auth()->id())->delete();
        else
            $context->savedByStudents()->create(['student_id' => auth()->id()]);
    }

    public function getSavedContexts($data , $student_id , $model , $with = [])
    {
        $records = $model::whereHas('savedByStudents', function($query) use ($student_id) {
            $query->where('student_id', $student_id);
        })->with($with);

        return getOrPaginate($records, $data);
    }

    public function searchForQuestions($data , $student_id)
    {
        $questions = Question::searchForMobile($data['search'] , $student_id)->with('answers');

        return getOrPaginate($questions, $data);
    }

    public function searchSavedLessons($data, $student_id)
    {
        return getOrPaginate(
            Lesson::published()->filter($data)->whereHas('savedByStudents', function($query) use ($student_id) {
                $query->where('student_id', $student_id);
            }), 
            $data
        );
    }
}
    