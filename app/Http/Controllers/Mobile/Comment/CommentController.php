<?php

namespace App\Http\Controllers\Mobile\Comment;

use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\CommentRequest;
use App\Http\Resources\Lesson\CommentResource;
use App\Services\Comment\CommentService;

class CommentController extends Controller
{
    public function __construct(
        protected CommentService $commentService,
    ) {}

    public function index(Request $request , $lesson_id)
    {
        return success(
            $this->commentService->getForMobile($request->all(), $lesson_id),
            ApiMessages::MSG_SUCCESS,
            CommentResource::class,
            $request->has('per_page')
        );
    }
    public function store(CommentRequest $request , $lesson_id)
    {
        return success(
            $this->commentService->store($request->validated(), $lesson_id),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function deleteComment($id)
    {
        return success(
            $this->commentService->deleteComment($id),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function replay(CommentRequest $request , $comment_id)
    {
        return success(
            $this->commentService->replay($request->validated(), $comment_id),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function deleteReplay($id)
    {
        return success(
            $this->commentService->deleteReplay($id),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function pinComment($id)
    {
        return success(
            $this->commentService->pinComment($id),
            ApiMessages::MSG_SUCCESS,
        );
    }
}
