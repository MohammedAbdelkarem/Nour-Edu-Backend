<?php

namespace App\Http\Controllers\Administration\Question;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Base\ChangePriorityRequest;
use App\Services\Question\QuestionService;
use App\Services\Answer\AnswerService;
use App\Http\Requests\Question\CreateQuestionRequest;
use App\Http\Requests\Question\UpdateQuestionRequest;
use App\Http\Requests\GetItemsRequest;
use App\Http\Resources\Question\QuestionResource;
use App\Http\Resources\Answer\AnswerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function __construct(
        protected QuestionService $questionService,
        protected AnswerService $answerService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return success(
            $this->questionService->getAll($request->all()),
            ApiMessages::MSG_SUCCESS,
            QuestionResource::class,
            $request->has('per_page')
        );
    }

    public function show($id): JsonResponse
    {
        return success(
            $this->questionService->show($id),
            ApiMessages::MSG_SUCCESS,
            QuestionResource::class
        );
    }

    public function store(CreateQuestionRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->questionService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update(UpdateQuestionRequest $request, $id): JsonResponse
    {
        return success(
            $this->questionService->update($request->validated(), $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function destroy($id): JsonResponse
    {
        return success(
            $this->questionService->destroy($id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function updateAnswerPriority(Request $request): JsonResponse
    {
        return success(
            $this->answerService->changePriority($request->validated()),
            ApiMessages::MSG_SUCCESS
        );
    }
}
