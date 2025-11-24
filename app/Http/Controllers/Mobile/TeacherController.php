<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Lesson\LessonService;
use App\Constants\ApiMessages;
use App\Http\Resources\Lesson\LessonResource;
use App\Services\Administration\Teacher\TeacherService;

class TeacherController extends Controller
{
    public function __construct(
        protected LessonService $lessonService,
        protected TeacherService $teacherService
    ) {}

    public function getTeacherLessons(Request $request)
    {
        return success(
            $this->lessonService->getTeacherLessons(auth()->id(), $request->all()),
            ApiMessages::MSG_SUCCESS,
            LessonResource::class,
            $request->has('per_page')
        );
    }

    public function getTeacherDetails()
    {
        return success(
            $this->teacherService->getTeacherDetailsForTeacherApp(auth()->id()),
            ApiMessages::MSG_SUCCESS,
        );
    }
}
