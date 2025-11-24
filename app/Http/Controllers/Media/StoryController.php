<?php

namespace App\Http\Controllers\Media;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\Media\MediaService;
use App\Services\Story\StoryService;
use App\Http\Requests\GetItemsRequest;
use App\Http\Resources\Story\StoryResource;
use App\Http\Requests\Story\CreateStoryRequest;
use App\Http\Requests\Story\UpdateStoryRequest;

class StoryController extends Controller
{
    public function __construct(
        protected StoryService $storyService,
        protected MediaService $mediaService,
    ) {}

    public function index(GetItemsRequest $request)
    {
        return success(
            $this->storyService->getAll( $request->validated()),
            ApiMessages::MSG_SUCCESS,
            StoryResource::class,
            $request->has('per_page')
        );
    }

    public function show($id)
    {
        return success(
            $this->storyService->show($id),
            ApiMessages::MSG_SUCCESS,
            StoryResource::class
        );
    }

    public function store(CreateStoryRequest $request)
    {
        return createdSuccess(
            $this->storyService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update(UpdateStoryRequest $request , $id)
    {
        return success(
            $this->storyService->update($request->validated() , $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function destroy($id)
    {
        return success(
            $this->storyService->destroy($id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changeStatus($id)
    {
        return success(
            $this->storyService->changeStatus($id),
            ApiMessages::MSG_SUCCESS
        );
    }
}
