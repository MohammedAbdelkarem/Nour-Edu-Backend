<?php

namespace App\Http\Controllers\System\Info;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\Info\FAQRequest;
use App\Http\Resources\System\Info\FaqCategoryResource;
use App\Http\Resources\System\Info\FAQResource;
use App\Services\System\Info\FAQService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    public function __construct(
        protected FAQService $FAQService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $search   = $request->search ?? "";
        $per_page = $request->per_page;

        return success(
            $this->FAQService->index(per_page: $per_page, search: $search),
            ApiMessages::MSG_SUCCESS,
            FaqCategoryResource::class,
            true
        );
    }

    public function indexAdmin(Request $request): JsonResponse
    {
        $search     = $request->search ?? "";
        $per_page   = $request->per_page;
        $category   = $request->category_id;
        return success(
            $this->FAQService->index($per_page, $category, $search),
            ApiMessages::MSG_SUCCESS,
            FaqCategoryResource::class,
            true
        );
    }

    public function store(FAQRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->FAQService->store($request->validated()),
            ApiMessages::MSG_CREATED
        );
    }

    public function show(string $id): JsonResponse
    {
        return success(
            $this->FAQService->show($id),
            ApiMessages::MSG_SUCCESS,
            FAQResource::class
        );
    }

    public function update(FAQRequest $request, string $id): JsonResponse
    {
        return success(
            $this->FAQService->update($request->validated(), $id),
            ApiMessages::MSG_UPDATED,
        );
    }

    public function destroy(string $id): JsonResponse
    {
        return success(
            $this->FAQService->destroy($id),
            ApiMessages::MSG_DELETED,
        );
    }
}
