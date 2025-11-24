<?php

namespace App\Http\Controllers\System\CustomerServiceCard;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\CustomerServiceCard\CustomerServiceCardRequest;
use App\Http\Resources\System\CustomerServiceCard\CustomerServiceCardResource;
use App\Services\System\CustomerServiceCard\CustomerServiceCardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerServiceCardController extends Controller
{
    public function __construct(
        protected CustomerServiceCardService $cardService
    ) {}


    public function getTypesStatus(): JsonResponse
    {
        return success(
            $this->cardService->getTypesStatus(),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function indexUser(Request $request): JsonResponse
    {
        $per_page = $request->per_page;
        $search = $request->search ?? "";
        $type   = $request->type;
        $status = $request->status;
        $my     = (bool) $request->my ?? false;

        return success(
            $this->cardService->indexUser($per_page, $search, $type, $status, $my),
            ApiMessages::MSG_SUCCESS,
            CustomerServiceCardResource::class,
            true
        );
    }

    public function indexAdmin(Request $request): JsonResponse
    {
        $per_page = $request->per_page;
        $search = $request->search ?? "";
        $type   = $request->type;
        $status = $request->status;

        return success(
            $this->cardService->indexAdmin($per_page, $search, $type, $status),
            ApiMessages::MSG_SUCCESS,
            CustomerServiceCardResource::class,
            true
        );
    }

    public function store(CustomerServiceCardRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->cardService->store($request->validated()),
            ApiMessages::MSG_CREATED
        );
    }

    public function showUser(string $id): JsonResponse
    {
        return success(
            $this->cardService->showUser($id),
            ApiMessages::MSG_SUCCESS,
            CustomerServiceCardResource::class
        );
    }

    public function showAdmin(string $id): JsonResponse
    {
        return success(
            $this->cardService->showAdmin($id),
            ApiMessages::MSG_SUCCESS,
            CustomerServiceCardResource::class
        );
    }

    public function update(CustomerServiceCardRequest $request, string $id): JsonResponse
    {
        return success(
            $this->cardService->update($id, $request->validated()),
            ApiMessages::MSG_UPDATED
        );
    }

    public function close(CustomerServiceCardRequest $request): JsonResponse
    {
        return success(
            $this->cardService->close($request->validated()),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function destroy(string $id): JsonResponse
    {
        return success(
            $this->cardService->destroy($id),
            ApiMessages::MSG_DELETED,
        );
    }

    public function destroyByAdmin(string $id): JsonResponse
    {;
        return success(
            $this->cardService->destroyByAdmin($id),
            ApiMessages::MSG_DELETED,
        );
    }
}
