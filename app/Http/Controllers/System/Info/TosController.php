<?php

namespace App\Http\Controllers\System\Info;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\Info\TosRequest;
use App\Http\Resources\System\Info\TosResource;
use App\Services\System\Info\TosService;
use Illuminate\Http\JsonResponse;

class TosController extends Controller
{
    public function __construct(
        protected TosService $tosService
    ) {}

    public function index(): JsonResponse
    {
        return success(
            $this->tosService->index(),
            ApiMessages::MSG_SUCCESS,
            TosResource::class,
        );
    }

    public function store(TosRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->tosService->store($request->validated()),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function show(): JsonResponse
    {
        return success(
            $this->tosService->show(request()->lang),
            ApiMessages::MSG_SUCCESS,
            TosResource::class
        );
    }
}
