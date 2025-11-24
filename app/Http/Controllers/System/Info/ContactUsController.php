<?php

namespace App\Http\Controllers\System\Info;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\Info\ContactUsRequest;
use App\Http\Resources\System\Info\ContactUsResource;
use App\Services\System\Info\ContactUsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function __construct(
        protected ContactUsService $contactUsService
    ) {}

    public function types(): JsonResponse
    {
        return success(
            $this->contactUsService->types(),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function index(): JsonResponse
    {
        return success(
            $this->contactUsService->index(),
            ApiMessages::MSG_SUCCESS,
            ContactUsResource::class,
        );
    }

    public function store(ContactUsRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->contactUsService->store($request->validated()),
            ApiMessages::MSG_CREATED
        );
    }

    public function show(string $id): JsonResponse
    {
        return success(
            $this->contactUsService->show($id),
            ApiMessages::MSG_SUCCESS,
            ContactUsResource::class
        );
    }

    public function update(ContactUsRequest $request, string $id): JsonResponse
    {
        return success(
            $this->contactUsService->update($request->validated(), $id),
            ApiMessages::MSG_UPDATED
        );
    }

    public function destroy(string $id): JsonResponse
    {
        return success(
            $this->contactUsService->destroy($id),
            ApiMessages::MSG_DELETED,
        );
    }
}
