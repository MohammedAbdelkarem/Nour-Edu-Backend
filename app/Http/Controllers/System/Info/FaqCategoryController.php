<?php

namespace App\Http\Controllers\System\Info;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\Info\FaqCategoryRequest;
use App\Http\Resources\System\Info\FaqCategoryListResource;
use App\Http\Resources\System\Info\FaqCategoryResource;
use App\Services\System\Info\FaqCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqCategoryController extends Controller
{
    public function __construct(
        protected FaqCategoryService $service
    ) {}

    public function apps(): JsonResponse
    {
        return success(
            $this->service->apps(),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function index(Request $request): JsonResponse
    {
        return success(
            $this->service->index($request->per_page, $request->search, $request->app),
            ApiMessages::MSG_SUCCESS,
            FaqCategoryListResource::class,
            true
        );
    }

    public function store(FaqCategoryRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->service->store($request->validated()),
            ApiMessages::MSG_CREATED
        );
    }

    public function show(string $id): JsonResponse
    {
        return success(
            $this->service->show($id),
            ApiMessages::MSG_SUCCESS,
            FaqCategoryResource::class
        );
    }

    public function update(FaqCategoryRequest $request, string $id): JsonResponse
    {
        return success(
            $this->service->update($id, $request->validated()),
            ApiMessages::MSG_UPDATED
        );
    }

    public function destroy(string $id): JsonResponse
    {
        return success(
            $this->service->destroy($id),
            ApiMessages::MSG_DELETED,
        );
    }
}
