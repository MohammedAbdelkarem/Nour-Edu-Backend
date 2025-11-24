<?php

namespace App\Http\Controllers\Media;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\Media\MediaService;
use App\Http\Requests\GetItemsRequest;
use App\Services\Banner\BannerService;
use App\Http\Resources\Banner\BannerResource;
use App\Http\Requests\Banner\CreateBannerRequest;
use App\Http\Requests\Banner\UpdateBannerRequest;

class BannerController extends Controller
{
    public function __construct(
        protected BannerService $bannerService,
        protected MediaService $mediaService,
    ) {}

    public function index(GetItemsRequest $request)
    {
        return success(
            $this->bannerService->getAll( $request->validated()),
            ApiMessages::MSG_SUCCESS,
            BannerResource::class,
            $request->has('per_page')
        );
    }

    public function show($id)
    {
        return success(
            $this->bannerService->show($id),
            ApiMessages::MSG_SUCCESS,
            BannerResource::class
        );
    }

    public function store(CreateBannerRequest $request)
    {
        return createdSuccess(
            $this->bannerService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update(UpdateBannerRequest $request , $id)
    {
        return success(
            $this->bannerService->update($request->validated() , $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function destroy($id)
    {
        return success(
            $this->bannerService->destroy($id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changeStatus($id)
    {
        return success(
            $this->bannerService->changeStatus($id),
            ApiMessages::MSG_SUCCESS
        );
    }
}
