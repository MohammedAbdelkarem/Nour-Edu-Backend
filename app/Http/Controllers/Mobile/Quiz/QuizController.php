<?php

namespace App\Http\Controllers\Mobile\Quiz;

use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;
use App\Services\Quiz\SolvingService;
use App\Http\Resources\Quiz\QuizResource;
use App\Http\Requests\Quiz\StartQuizRequest;
use App\Http\Requests\Quiz\SubmitQuizSolutionRequest;
use App\Http\Resources\Quiz\QuizResultResource;

class QuizController extends Controller
{
    public function __construct(
        protected QuizService $quizService,
        protected SolvingService $solvingService,
    ) {}

    public function search(Request $request)
    {
        return success(
            $this->quizService->search($request->all(), $request->student_id ?? auth()->id()),
            ApiMessages::MSG_SUCCESS,
            QuizResource::class,
            $request->has('per_page')
        );
    }

    public function filter(Request $request)
    {
        return success(
            $this->quizService->filter($request->all(), $request->student_id ?? auth()->id()),
            ApiMessages::MSG_SUCCESS,
            QuizResource::class,
            $request->has('per_page')
        );
    }

    public function getPurchasedQuizzes(Request $request)
    {
        return success(
            $this->quizService->getPurchasedQuizzes($request->student_id ?? auth()->id(), $request->context_type, $request->all()),
            ApiMessages::MSG_SUCCESS,
            QuizResource::class,
            $request->has('per_page')
        );
    }

    public function startQuiz($id)
    {
        return success(
            $this->solvingService->startQuiz($id),
            ApiMessages::MSG_SUCCESS,
            QuizResultResource::class
        );
    }

    public function solveQuiz(SubmitQuizSolutionRequest $request)
    {
        return success(
            $this->solvingService->solveQuiz($request->validated()),
            ApiMessages::MSG_SUCCESS,
            QuizResultResource::class
        );
    }

    public function getPrevSolution($quiz_id)
    {
        return success(
            $this->solvingService->showPrevSolution($quiz_id),
            ApiMessages::MSG_SUCCESS,
            QuizResultResource::class
        );
    }

    public function show($id)
    {
        return success(
            $this->quizService->show($id),
            ApiMessages::MSG_SUCCESS,
            QuizResource::class
        );
    }
    
}
