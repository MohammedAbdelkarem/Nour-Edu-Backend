<?php

namespace App\Http\Controllers\Administration\Unit;

use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Services\Unit\UnitService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Unit\UnitResource;
use App\Http\Requests\Unit\CreateUnitRequest;
use App\Http\Requests\Unit\UpdateUnitRequest;
use App\Http\Requests\Base\ChangePriorityRequest;

class UnitController extends Controller
{
    public function __construct(
        protected UnitService $unitService,
    ) {}

    public function index(Request $request)
    {   
        return success(
            $this->unitService->getAll($request->all()),
            ApiMessages::MSG_SUCCESS,
            UnitResource::class,
            $request->has('per_page')
        );
    }

    public function store(CreateUnitRequest $request)
    {
        return createdSuccess(
            $this->unitService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update(UpdateUnitRequest $request, $id)
    {
        return success(
            $this->unitService->update($request->validated(), $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function destroy($id)
    {
        return success(
            $this->unitService->destroy($id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changePublishStatus(Request $request, $id)
    {
        return success(
            $this->unitService->changePublishStatus($id, $request->status),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changeAccessTypeStatus(Request $request, $id)
    {
        return success(
            $this->unitService->changeAccessTypeStatus($id, $request->price ?? 0),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changePriority(ChangePriorityRequest $request)
    {
        return success(
            $this->unitService->changePriority($request->validated()['context']),
            ApiMessages::MSG_SUCCESS
        );
    }
}
