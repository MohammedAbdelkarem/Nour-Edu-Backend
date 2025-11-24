<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AppVersionService;
use App\Constants\ApiMessages;
use App\Http\Resources\AppVersionResource;

class AppVersionController extends Controller
{
    public function __construct(
        protected AppVersionService $appVersionService,
    ) {}

    public function indexStudent(Request $request)
    {
        return success(
            $this->appVersionService->studentVersions($request->all()), ApiMessages::MSG_SUCCESS,
            AppVersionResource::class,
            $request->has('per_page')
        );
    }

    public function indexTeacher(Request $request)
    {
        return success(
            $this->appVersionService->teacherVersions($request->all()), ApiMessages::MSG_SUCCESS,
            AppVersionResource::class,
            $request->has('per_page')
        );
    }
}
