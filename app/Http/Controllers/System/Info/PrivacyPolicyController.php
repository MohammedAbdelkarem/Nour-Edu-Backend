<?php

namespace App\Http\Controllers\System\Info;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\Info\PrivacyPolicyRequest;
use App\Http\Resources\System\Info\PrivacyPolicyResource;
use App\Services\System\Info\PrivacyPolicyService;
use Illuminate\Http\JsonResponse;

class PrivacyPolicyController extends Controller
{
    public function __construct(
        protected PrivacyPolicyService $privacyPolicyService
    ) {}

    public function index(): JsonResponse
    {
        return success(
            $this->privacyPolicyService->index(),
            ApiMessages::MSG_SUCCESS,
            PrivacyPolicyResource::class,
        );
    }

    public function store(PrivacyPolicyRequest $request): JsonResponse
    {
        return createdSuccess(
            $this->privacyPolicyService->store($request->validated()),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function show(): JsonResponse
    {
        return success(
            $this->privacyPolicyService->show(request()->lang),
            ApiMessages::MSG_SUCCESS,
            PrivacyPolicyResource::class
        );
    }
}
