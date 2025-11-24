<?php

namespace App\Http\Controllers\Administration\SubUnit;

use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\SubUnit\SubUnitService;
use App\Http\Resources\SubUnit\SubUnitResource;
use App\Http\Requests\Base\ChangePriorityRequest;
use App\Http\Requests\SubUnit\CreateSubUnitRequest;
use App\Http\Requests\SubUnit\UpdateSubUnitRequest;

class SubUnitController extends Controller
{
    public function __construct(
        protected SubUnitService $subUnitService,
    ) {}

    public function index(Request $request)
    {   
        return success(
            $this->subUnitService->getAll($request->all()),
            ApiMessages::MSG_SUCCESS,
            SubUnitResource::class,
            $request->has('per_page')
        );
    }

    public function store(CreateSubUnitRequest $request)
    {
        return createdSuccess(
            $this->subUnitService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update(UpdateSubUnitRequest $request, $id)
    {
        return success(
            $this->subUnitService->update($request->validated(), $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function destroy($id)
    {
        return success(
            $this->subUnitService->destroy($id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changePublishStatus(Request $request, $id)
    {
        return success(
            $this->subUnitService->changePublishStatus($id, $request->status),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changePriority(ChangePriorityRequest $request)
    {
        return success(
            $this->subUnitService->changePriority($request->validated()['context']),
            ApiMessages::MSG_SUCCESS
        );
    }
}
