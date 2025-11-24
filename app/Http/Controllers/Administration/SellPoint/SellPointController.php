<?php

namespace App\Http\Controllers\Administration\SellPoint;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SellPoint\SellPointService;
use App\Http\Resources\SellPointResource;
use App\Constants\ApiMessages;
use App\Http\Requests\SellPoint\CreateSellPointRequest;
use App\Http\Requests\SellPoint\UpdateSellPointRequest;

class SellPointController extends Controller
{
    public function __construct(
        protected SellPointService $sellPointService,
    ) {}

    public function index(Request $request)
    {
        return success(
            $this->sellPointService->getAll($request->all()),
            ApiMessages::MSG_SUCCESS,
            SellPointResource::class,
            $request->has('per_page')
        );
    }

    public function store(CreateSellPointRequest $request)
    {
        return createdSuccess(
            $this->sellPointService->create($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update(UpdateSellPointRequest $request, $id)
    {
        return success(
            $this->sellPointService->update($request->validated(), $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function destroy($id)
    {
        return success(
            $this->sellPointService->destroy($id),
            ApiMessages::MSG_SUCCESS
        );
    }
}
