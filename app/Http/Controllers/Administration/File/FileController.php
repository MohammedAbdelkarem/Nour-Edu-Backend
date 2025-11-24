<?php

namespace App\Http\Controllers\Administration\File;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\File\FileService;
use Illuminate\Http\Request;
use App\Http\Resources\File\FileResource;
use App\Http\Requests\File\CreateFileRequest;
use App\Http\Requests\File\UpdateFileRequest;
use App\Http\Requests\Base\ChangePriorityRequest;

class FileController extends Controller
{
    public function __construct(
        protected FileService $fileService,
    ) {}

    public function index(Request $request)
    {   
        return success(
            $this->fileService->getAll($request->all()),
            ApiMessages::MSG_SUCCESS,
            FileResource::class,
            $request->has('per_page')
        );
    }

    public function store(CreateFileRequest $request)
    {
        return createdSuccess(
            $this->fileService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update(UpdateFileRequest $request, $id)
    {
        return success(
            $this->fileService->update($request->validated(), $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function destroy($id)
    {
        return success(
            $this->fileService->destroy($id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changePublishStatus(Request $request, $id)
    {
        return success(
            $this->fileService->changePublishStatus($id, $request->status),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changePriority(ChangePriorityRequest $request)
    {
        return success(
            $this->fileService->changePriority($request->validated()['context']),
            ApiMessages::MSG_SUCCESS
        );
    }
}
