<?php

namespace App\Http\Controllers\Administration\CLevel;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\CLevel\CLevelService;
use Illuminate\Http\Request;
use App\Http\Resources\CLevel\CLevelResource;
use App\Http\Requests\CLevel\CreateCLevelRequest;
use App\Http\Requests\CLevel\UpdateCLevelRequest;

class CLevelController extends Controller
{
    public function __construct(
        protected CLevelService $cLevelService,
    ) {}

    public function index(Request $request)
    {
        return success(
            $this->cLevelService->getAll($request->all()),
            ApiMessages::MSG_SUCCESS,
            CLevelResource::class,
            $request->has('per_page')
        );
    }

    public function store(CreateCLevelRequest $request)
    {
        return createdSuccess(
            $this->cLevelService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update(UpdateCLevelRequest $request, $id)
    {
        return success(
            $this->cLevelService->update($request->validated(), $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function destroy($id)
    {
        return success(
            $this->cLevelService->destroy($id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changePublishStatus(Request $request, $id)
    {
        return success(
            $this->cLevelService->changePublishStatus($id , $request->status),
            ApiMessages::MSG_SUCCESS
        );
    }
}
