<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Constants\ApiMessages;
use App\Http\Resources\Lesson\LessonResource;
use Illuminate\Http\Request;
use App\Services\Lesson\LessonService;

class DownloadController extends Controller
{
    public function __construct(
        protected LessonService $lessonService
    ) {}

    public function index()
    {
        return success(
            $this->lessonService->getDownloads(auth()->id()),
            ApiMessages::MSG_SUCCESS,
            LessonResource::class,
        );
    }

    public function download($lesson_id)
    {
       return success(
            $this->lessonService->downloadLesson($lesson_id, auth()->id()),
            ApiMessages::MSG_SUCCESS,
       );
    }
}
