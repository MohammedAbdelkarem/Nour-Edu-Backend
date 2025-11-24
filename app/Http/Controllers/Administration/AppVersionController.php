<?php

namespace App\Http\Controllers\Administration;

use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Services\AppVersionService;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppVersionResource;
use App\Http\Requests\CreateAppVersionRequest;

class AppVersionController extends Controller
{
    public function __construct(
        protected AppVersionService $appVersionService,
    ) {}

    public function index(Request $request)
    {
        return success(
            $this->appVersionService->index($request->all()), ApiMessages::MSG_SUCCESS,
            AppVersionResource::class,
            $request->has('per_page')
        );
    }

    public function store(CreateAppVersionRequest $request)
    {
        return createdSuccess($this->appVersionService->store($request->validated()), ApiMessages::MSG_SUCCESS);
    }

    public function destroy($id)
    {
        return success($this->appVersionService->delete($id), ApiMessages::MSG_SUCCESS);
    }
}
