<?php

namespace App\Http\Controllers\Mobile;

use App\Models\Unit;
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\CLevel\CLevelService;
use App\Services\ELevel\ELevelService;
use App\Services\Lesson\LessonService;
use App\Services\Country\CountryService;
use App\Http\Resources\List\ListResource;
use App\Http\Resources\SellPointResource;
use App\Http\Resources\Unit\UnitResource;
use App\Services\SellPoint\SellPointService;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Lesson\LessonResource;
use App\Services\Hierarichy\HierarichyService;
use App\Http\Resources\Subject\SubjectResource;
use App\Http\Resources\SubUnit\SubUnitResource;
use App\Http\Resources\Teacher\TeacherResource;
use App\Services\Administration\ResponsibilityService;
use App\Services\Administration\Teacher\TeacherService;
use App\Http\Resources\Responsibility\ResponsibilityResource;

class HierarichyController extends Controller
{
    public function __construct(
        protected HierarichyService $hierarichyService,
        protected ResponsibilityService $responsibilityService,
        protected LessonService $lessonService,
        protected TeacherService $teacherService,
        protected ELevelService $eLevelService,
        protected CLevelService $cLevelService,
        protected CountryService $countryService,
        protected SellPointService $sellPointService,
    ) {}

    public function countries()
    {
        return success($this->countryService->getList(), ApiMessages::MSG_SUCCESS);
    }

    public function e_levels($contry_id = null)
    {
        return success($this->eLevelService->getList($contry_id), ApiMessages::MSG_SUCCESS);
    }

    public function c_levels($e_level_id)
    {
        return success($this->cLevelService->getList($e_level_id), ApiMessages::MSG_SUCCESS);
    }

    public function getSubject($subject_id)
    {
        return success(
            $this->hierarichyService->getSubject($subject_id),
            ApiMessages::MSG_SUCCESS,
            SubjectResource::class,
        );
    }
    

    public function getUnit($unit_id)
    {
        return success(
            $this->hierarichyService->getUnit($unit_id),
            ApiMessages::MSG_SUCCESS,
            UnitResource::class,
        );
    }

    public function getSubUnit($sub_unit_id)
    {
        return success(
            $this->hierarichyService->getSubUnit($sub_unit_id),
            ApiMessages::MSG_SUCCESS,
            SubUnitResource::class,
        );
    }

    public function getUnitDetails($unit_id)
    {
        return success(
            $this->hierarichyService->getUnitDetails($unit_id),
            ApiMessages::MSG_SUCCESS,
            UnitResource::class,
        );
    }

    public function getResponsibilitiesByTeacherId(Request $request , $teacher_id , $c_level_id)
    {
        return success(
            $this->responsibilityService->getResponsibilitiesByTeacherId($request->all() , $teacher_id , true , $c_level_id),
            ApiMessages::MSG_SUCCESS,
            ResponsibilityResource::class,
            $request->has('per_page')
        );
    }

    public function recordLessonView($lesson_id)
    {
        $student_id = auth()->id();
        
        $this->lessonService->recordLessonView($lesson_id, $student_id);
        
        return success(
            [],
            ApiMessages::MSG_SUCCESS
        );
    }

    public function getTeacherDetails($teacher_id)
    {
        return success(
            $this->teacherService->getTeacherDetails($teacher_id),
            ApiMessages::MSG_SUCCESS,
            TeacherResource::class,
        );
    }

    public function getPurchasedCourses(Request $request)
    {
        return success(
            $this->hierarichyService->getPurchasedContextByModel($request->student_id ?? auth()->id(), $request->all(), Course::class, 'publishedSubjects'),
            ApiMessages::MSG_SUCCESS,
            CourseResource::class,
            $request->has('per_page')
        );
    }

    public function getPurchasedSubjects(Request $request)
    {
        return success(
            $this->hierarichyService->getPurchasedContextByModel($request->student_id ?? auth()->id(), $request->all(), Subject::class, 'publishedUnits'),
            ApiMessages::MSG_SUCCESS,
            SubjectResource::class,
            $request->has('per_page')
        );
    }

    public function getPurchasedUnits(Request $request)
    {
        return success(
            $this->hierarichyService->getPurchasedContextByModel($request->student_id ?? auth()->id(), $request->all(), Unit::class, ['publishedSubUnits', 'publishedQuizzes', 'publishedFiles']),
            ApiMessages::MSG_SUCCESS,
            UnitResource::class,
            $request->has('per_page')
        );
    }

    public function getPurchasedListsByType(Request $request)
    {
        $model = getModel($request->context_type);

        return success(
            $this->hierarichyService->getPurchasedContextByModel($request->student_id ?? auth()->id(), $request->all(), $model , []),
            ApiMessages::MSG_SUCCESS,
            ListResource::class,
        );
    }

    public function rateLesson(Request $request, $lesson_id)
    {
        return success(
            $this->lessonService->rateLesson($lesson_id, $request->rate),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function getLesson($lesson_id)
    {
        return success(
            $this->lessonService->showForStudent($lesson_id),
            ApiMessages::MSG_SUCCESS,
            LessonResource::class,
        );
    }

    public function getSellPoints(Request $request)
    {
        return success(
            $this->sellPointService->getAll($request->all()),
            ApiMessages::MSG_SUCCESS,
            SellPointResource::class,
            $request->has('per_page')
        );
    }
}
