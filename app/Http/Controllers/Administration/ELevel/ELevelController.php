<?php

namespace App\Http\Controllers\Administration\ELevel;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\ELevel\ELevelService;
use Illuminate\Http\Request;
use App\Http\Resources\ELevel\ELevelResource;
use App\Http\Requests\ELevel\CreateELevelRequest;
use App\Http\Requests\ELevel\UpdateELevelRequest;

class ELevelController extends Controller
{
    public function __construct(
        protected ELevelService $eLevelService,
    ) {}

    public function index(Request $request)
    {
        return success(
            $this->eLevelService->getAll($request->all()),
            ApiMessages::MSG_SUCCESS,
            ELevelResource::class,
            $request->has('per_page')
        );
    }

    public function show($id)
    {
        return success(
            $this->eLevelService->show($id),
            ApiMessages::MSG_SUCCESS,
            ELevelResource::class
        );
    }

    public function store(CreateELevelRequest $request)
    {
        return createdSuccess(
            $this->eLevelService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
            ELevelResource::class
        );
    }

    public function update(UpdateELevelRequest $request, $id)
    {
        return success(
            $this->eLevelService->update($request->validated(), $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function destroy($id)
    {
        return success(
            $this->eLevelService->destroy($id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changePublishStatus(Request $request, $id)
    {
        return success(
            $this->eLevelService->changePublishStatus($id , $request->status),
            ApiMessages::MSG_SUCCESS
        );
    }
}
