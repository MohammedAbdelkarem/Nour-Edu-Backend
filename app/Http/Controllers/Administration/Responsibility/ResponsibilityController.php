<?php

namespace App\Http\Controllers\Administration\Responsibility;

use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Responsibility\LinkingRequest;
use App\Services\Administration\ResponsibilityService;
use App\Http\Resources\Responsibility\ResponsibilityResource;

class ResponsibilityController extends Controller
{
    public function __construct(
        protected ResponsibilityService $responsibilityService,
    ) {}

    public function attach(LinkingRequest $request)
    {
        return success(
            $this->responsibilityService->linking($request->validated() , 'create'),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function detach(LinkingRequest $request)
    {
        return success(
            $this->responsibilityService->linking($request->validated() , 'delete'),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function getResponsibilitiesByTeacherId(Request $request , $teacher_id)
    {
        return success(
            $this->responsibilityService->getResponsibilitiesByTeacherId($request->all() , $teacher_id),
            ApiMessages::MSG_SUCCESS,
            ResponsibilityResource::class,
            $request->has('per_page')
        );
    }
}