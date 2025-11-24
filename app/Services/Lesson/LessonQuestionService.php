<?php

namespace App\Services\Lesson;

use App\Models\Lesson;
use App\Models\LessonQuestion;
use App\Constants\MediaCollection;

/**
 * Class LessonQuestionService.
 */
class LessonQuestionService
{
    public function get($data , $lesson_id)
    {
        $questions = LessonQuestion::where('lesson_id', $lesson_id)
            ->with(['teacher', 'student' , 'lesson']);

        return getOrPaginate($questions, $data);
    }

    public function getForStudent($data , $lesson_id , $student_id)
    {
        $questions = LessonQuestion::where('lesson_id', $lesson_id)
            ->where('student_id', $student_id)
            ->with(['teacher', 'student' , 'lesson']);

        return getOrPaginate($questions, $data);
    }

    public function ask($data , $lesson_id)
    {
        $question = LessonQuestion::create([
            'lesson_id' => $lesson_id,
            'student_id' => auth()->id(),
            'question' => $data['text'],
            'teacher_id' => $this->getLessonTeacher($lesson_id),
        ]);

        if(isset($data['images']))
            uploadFilesOnMedia($data['images'] , $question , MediaCollection::LESSON_QUESTION_COLLECTION);
    }

    public function answer($data , $question_id)
    {
        $question = LessonQuestion::findByIdOrFail($question_id);
        $question->answer = $data['text'];
        $question->save();

        if(isset($data['images']))
            uploadFilesOnMedia($data['images'] , $question , MediaCollection::LESSON_QUESTION_ANSWER_COLLECTION);
    }

    public function getPurchasedleLessons($student_id)
    {
        return Lesson::published()
            ->wherehas('unlockedContexts' , function($query) use ($student_id) {
                $query->where('user_id', $student_id);
            })
            ->get();
    }

    private function getLessonTeacher($lesson_id)
    {
        $lesson = Lesson::findByIdOrFail($lesson_id , ['unit.responsibilities.teacher']);

        return $lesson->unit->responsibilities->first()->teacher->id;
    }
}
