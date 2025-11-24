<?php

namespace App\Http\Controllers\Administration\Quiz;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\Quiz\QuizService;
use App\Http\Requests\Quiz\CreateQuizRequest;
use App\Http\Requests\Quiz\UpdateQuizRequest;
use App\Http\Requests\Base\ChangePriorityRequest;
use App\Http\Resources\Quiz\QuizResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(
        protected QuizService $quizService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return success(
            $this->quizService->getAll($request->all()),
            ApiMessages::MSG_SUCCESS,
            QuizResource::class,
            $request->has('per_page')
        );
    }

    public function show($id): JsonResponse
    {
        return success(
            $this->quizService->show($id),
            ApiMessages::MSG_SUCCESS,
            QuizResource::class
        );
    }

    public function store(CreateQuizRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->quizService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update(UpdateQuizRequest $request, $id): JsonResponse
    {
        return success(
            $this->quizService->update($request->validated(), $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function destroy($id): JsonResponse
    {
        return success(
            $this->quizService->destroy($id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changePublishStatus(Request $request, $id): JsonResponse
    {
        return success(
            $this->quizService->changePublishStatus($id, $request->status),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changePriority(ChangePriorityRequest $request): JsonResponse
    {
        return success(
            $this->quizService->changePriority($request->validated()['context']),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function detachQuestionsFromQuiz(Request $request, $id): JsonResponse
    {
        return success(
            $this->quizService->detachQuestionsFromQuiz($id, $request->question_ids),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function attachQuestionsToQuiz(Request $request, $id): JsonResponse
    {
        return success(
            $this->quizService->attachQuestionsToQuiz($id, $request->question_ids),
            ApiMessages::MSG_SUCCESS
        );
    }

}
