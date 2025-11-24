<?php

namespace App\Http\Controllers\System\Info;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\Info\CityRequest;
use App\Http\Resources\System\Info\CityResource;
use App\Services\System\Info\CityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function __construct(
        protected CityService $cityService
    ) {}

    public function index(Request $request , $country_id = null): JsonResponse
    {
        return success(
            $this->cityService->index($request->per_page, $request->search, $country_id),
            ApiMessages::MSG_SUCCESS,
            CityResource::class,
            $request->per_page > 0
        );
    }

    public function store(CityRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->cityService->store($request->validated()),
            ApiMessages::MSG_CREATED
        );
    }

    public function show(string $id): JsonResponse
    {
        return success(
            $this->cityService->show($id),
            ApiMessages::MSG_SUCCESS,
            CityResource::class
        );
    }

    public function update(CityRequest $request, string $id): JsonResponse
    {
        return success(
            $this->cityService->update($request->validated(), $id),
            ApiMessages::MSG_UPDATED
        );
    }

    public function destroy(string $id): JsonResponse
    {
        return success(
            $this->cityService->destroy($id),
            ApiMessages::MSG_DELETED,
        );
    }
}
