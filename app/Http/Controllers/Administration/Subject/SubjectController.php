<?php

namespace App\Http\Controllers\Administration\Subject;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\Subject\SubjectService;
use Illuminate\Http\Request;
use App\Http\Resources\Subject\SubjectResource;
use App\Http\Requests\Subject\CreateSubjectRequest;
use App\Http\Requests\Subject\UpdateSubjectRequest;

class SubjectController extends Controller
{
    public function __construct(
        protected SubjectService $subjectService,
    ) {}

    public function index(Request $request)
    {   
        return success(
            $this->subjectService->getAll($request->all()),
            ApiMessages::MSG_SUCCESS,
            SubjectResource::class,
            $request->has('per_page')
        );
    }

    public function store(CreateSubjectRequest $request)
    {
        return createdSuccess(
            $this->subjectService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update(UpdateSubjectRequest $request, $id)
    {
        return success(
            $this->subjectService->update($request->validated(), $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function destroy($id)
    {
        return success(
            $this->subjectService->destroy($id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changePublishStatus(Request $request, $id)
    {
        return success(
            $this->subjectService->changePublishStatus($id, $request->status),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changeAccessTypeStatus(Request $request, $id)
    {
        return success(
            $this->subjectService->changeAccessTypeStatus($id, $request->price ?? 0),
            ApiMessages::MSG_SUCCESS
        );
    }
}
