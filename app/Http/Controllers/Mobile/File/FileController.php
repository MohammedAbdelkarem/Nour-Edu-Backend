<?php

namespace App\Http\Controllers\Mobile\File;

use App\Models\Subject;
use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Services\File\FileService;
use App\Http\Controllers\Controller;
use App\Http\Resources\File\FileResource;

class FileController extends Controller
{
    public function __construct(
        protected FileService $fileService,
    ) {}

    public function search(Request $request)
    {
        return success(
            $this->fileService->search($request->all(), $request->student_id ?? auth()->id()),
            ApiMessages::MSG_SUCCESS,
            FileResource::class,
            $request->has('per_page')
        );
    }

    public function filter(Request $request)
    {
        return success(
            $this->fileService->filter($request->all(), $request->student_id ?? auth()->id()),
            ApiMessages::MSG_SUCCESS,
            FileResource::class,
            $request->has('per_page')
        );
    }

    public function getPurchasedFiles(Request $request)
    {
        return success(
            $this->fileService->getPurchasedFiles($request->student_id ?? auth()->id(), $request->context_type, $request->all()),
            ApiMessages::MSG_SUCCESS,
            FileResource::class,
            $request->has('per_page')
        );
    }
}
