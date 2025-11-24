<?php

namespace App\Services;

use App\Http\Resources\Teacher\TeacherResource;
use App\Http\Resources\Question\QuestionResource;
use App\Http\Resources\Lesson\LessonResource;
use App\Http\Resources\LessonQuestion\LessonQuestionResource;
use App\Models\LessonQuestion;
use App\Models\User;
use App\Services\Administration\Teacher\TeacherService;
use App\Services\Lesson\LessonService;

/**
 * Class TeacherHomeService.
 */
class TeacherHomeService
{
    public function __construct(
        protected LessonService $lessonService
    ) {}

    public function home()
    {
        $profile = TeacherResource::make(User::findByIdOrFail(auth()->id()) , ['responsibilities']);

        $lessons = $this->lessonService->getTeacherLessons(auth()->id() , []);

        $latestQuestions = LessonQuestion::where('teacher_id', auth()->id())
            ->latest('id')
            ->take(10)
            ->get();

        return [
            'profile' => $profile,
            'lessons' => LessonResource::collection($lessons),
            'latestQuestions' => LessonQuestionResource::collection($latestQuestions),
        ];
    }
}
