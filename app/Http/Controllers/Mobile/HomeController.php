<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Resources\Subject\SubjectResource;
use App\Http\Resources\Teacher\TeacherResource;
use App\Services\Administration\Teacher\TeacherService;
use App\Services\StudentHomeService;
use App\Services\TeacherHomeService;

class HomeController extends Controller
{
    public function __construct(
        protected StudentHomeService $studentHomeService,
        protected TeacherService $teacherService,
        protected TeacherHomeService $teacherHomeService
    ) {}

    public function home()
    {
        return success(
            $this->studentHomeService->get(),
            ApiMessages::MSG_SUCCESS
        );
    }

    //to get the homePage data for the other elevels and clevels
    public function othersHome($clevel_id)
    {
        return success(
            $this->studentHomeService->get($clevel_id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function search(Request $request)
    {
        return success(
            $this->studentHomeService->search($request->all()),
            ApiMessages::MSG_SUCCESS,
            SubjectResource::class,
            $request->has('per_page')
        );
    }

    public function getOnboarding()
    {
        return success(
            $this->teacherService->getRandom(),
            ApiMessages::MSG_SUCCESS,
            TeacherResource::class,
        );
    }

    public function getTeacherHome()
    {
        return success(
            $this->teacherHomeService->home(),
            ApiMessages::MSG_SUCCESS,
        );
    }
}
