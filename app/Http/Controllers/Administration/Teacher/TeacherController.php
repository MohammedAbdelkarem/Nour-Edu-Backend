<?php

namespace App\Http\Controllers\Administration\Teacher;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\Administration\Teacher\TeacherService;
use App\Http\Requests\GetItemsRequest;
use App\Http\Requests\Administration\Teacher\CreateTeacherRequest;
use App\Http\Requests\Administration\Teacher\UpdateTeacherReqeust;
use App\Http\Requests\Administration\Teacher\UpdateTeacherRequest;
use App\Http\Resources\Teacher\TeacherResource;
use App\Http\Resources\User\UserResource;

class TeacherController extends Controller
{
    public function __construct(
        protected TeacherService $teacherService,
    ) {}

    public function index(GetItemsRequest $request)
    {
        return success(
            $this->teacherService->getAll($request->all()),
            ApiMessages::MSG_SUCCESS,
            TeacherResource::class,
            $request->has('per_page')
        );
    }

    public function show($id)
    {
        return success(
            $this->teacherService->show($id),
            ApiMessages::MSG_SUCCESS,
            UserResource::class,
        );
    }

    public function store(CreateTeacherRequest $request)
    {
        return createdSuccess(
            $this->teacherService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update(UpdateTeacherReqeust $request, $id)
    {
        return success(
            $this->teacherService->update($request->validated(), $id),
            ApiMessages::MSG_SUCCESS,
        );
    }
}
