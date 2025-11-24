<?php

namespace App\Http\Controllers\Administration\Lesson;

use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\Lesson\LessonService;
use App\Services\Comment\CommentService;
use App\Http\Resources\Lesson\LessonResource;
use App\Http\Requests\Base\ChangePriorityRequest;
use App\Http\Requests\Lesson\CreateLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use App\Http\Requests\Lesson\UploadLessonVideosRequest;

class LessonController extends Controller
{
    public function __construct(
        protected LessonService $lessonService,
        protected CommentService $commentService,
    ) {}

    public function index(Request $request)
    {   
        return success(
            $this->lessonService->getAll($request->all()),
            ApiMessages::MSG_SUCCESS,
            LessonResource::class,
            $request->has('per_page')
        );
    }

    public function store(CreateLessonRequest $request)
    {
        return createdSuccess(
            $this->lessonService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function show($lesson_id)
    {
        return success(
            $this->lessonService->showForAdmin($lesson_id),
            ApiMessages::MSG_SUCCESS,
            LessonResource::class,
        );
    }

    public function update(UpdateLessonRequest $request, $id)
    {
        return success(
            $this->lessonService->update($request->validated(), $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function destroy($id)
    {
        return success(
            $this->lessonService->destroy($id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changePublishStatus(Request $request, $id)
    {
        return success(
            $this->lessonService->changePublishStatus($id, $request->status),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function uploadVideos(UploadLessonVideosRequest $request, $id)
    {
        return success(
            $this->lessonService->uploadVideos($request->validated(), $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changePriority(ChangePriorityRequest $request)
    {
        return success(
            $this->lessonService->changePriority($request->validated()['context']),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function deleteComment($id)
    {
        return success(
            $this->commentService->deleteComment($id),
            ApiMessages::MSG_SUCCESS
        );
    }
}
