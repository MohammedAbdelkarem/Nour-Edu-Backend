<?php

namespace App\Http\Controllers\Mobile\Saved;

use App\Models\Lesson;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Resources\Lesson\LessonResource;
use App\Http\Requests\Saved\SaveToggleRequest;
use App\Http\Resources\Question\QuestionResource;
use App\Services\SavedContext\SavedContextService;

class SavedContextController extends Controller
{
    public function __construct(
        protected SavedContextService $savedContextService
    ) {}

    public function getSavedLessons(Request $request)
    {
        return success(
            $this->savedContextService->getSavedContexts($request->all(), $request->student_id ?? auth()->id(), Lesson::class),
            ApiMessages::MSG_SUCCESS,
            LessonResource::class,
            $request->has('per_page')
        );
    }

    public function getSavedQuestions(Request $request)
    {
        return success(
            $this->savedContextService->getSavedContexts($request->all(), $request->student_id ?? auth()->id(), Question::class , ['answers']),
            ApiMessages::MSG_SUCCESS,
            QuestionResource::class,
            $request->has('per_page')
        );
    }

    public function saveToggle(SaveToggleRequest $request)
    {
        return success(
            $this->savedContextService->toggle($request->validated()),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function searchForQuestions(Request $request)
    {
        return success(
            $this->savedContextService->searchForQuestions($request->all(), $request->student_id ?? auth()->id()),
            ApiMessages::MSG_SUCCESS,
            QuestionResource::class,
            $request->has('per_page')
        );
    }

    public function searchSavedLessons(Request $request)
    {
        return success(
            $this->savedContextService->searchSavedLessons($request->all(), $request->student_id ?? auth()->id()),
            ApiMessages::MSG_SUCCESS,
            LessonResource::class,
            $request->has('per_page')
        );
    }
}
