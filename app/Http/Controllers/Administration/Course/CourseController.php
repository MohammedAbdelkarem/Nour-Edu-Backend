<?php

namespace App\Http\Controllers\Administration\Course;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\Course\CourseService;
use Illuminate\Http\Request;
use App\Http\Resources\Course\CourseResource;
use App\Http\Requests\Course\CreateCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;

class CourseController extends Controller
{
    public function __construct(
        protected CourseService $courseService,
    ) {}

    public function index(Request $request, $cLevelId = null)
    {
        $data = $request->all();
        
        // If CLevel ID is provided in URL, add it to the data
        if ($cLevelId !== null) {
            $data['c_level_id'] = $cLevelId;
        }
        
        return success(
            $this->courseService->getAll($data),
            ApiMessages::MSG_SUCCESS,
            CourseResource::class,
            $request->has('per_page')
        );
    }

    public function store(CreateCourseRequest $request)
    {
        return createdSuccess(
            $this->courseService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update(UpdateCourseRequest $request, $id)
    {
        return success(
            $this->courseService->update($request->validated(), $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function destroy($id)
    {
        return success(
            $this->courseService->destroy($id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changePublishStatus(Request $request, $id)
    {
        return success(
            $this->courseService->changePublishStatus($id , $request->status),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function changeAccessTypeStatus(Request $request, $id)
    {
        return success(
            $this->courseService->changeAccessTypeStatus($id, $request->price ?? 0),
            ApiMessages::MSG_SUCCESS
        );
    }
}
