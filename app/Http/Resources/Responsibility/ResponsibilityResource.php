<?php

namespace App\Http\Resources\Responsibility;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Constants\MediaCollection;
use App\Http\Resources\Unit\UnitResource;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Media\MediaResource;
use App\Http\Resources\CLevel\CLevelResource;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\ELevel\ELevelResource;
use App\Http\Resources\Lesson\LessonResource;
use App\Http\Resources\Subject\SubjectResource;
use App\Http\Resources\SubUnit\SubUnitResource;
use App\Http\Resources\Teacher\TeacherResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ResponsibilityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'teacher_id' => $this->teacher_id,
            'e_level_id' => $this->e_level_id,
            'c_level_id' => $this->c_level_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'unit_id' => $this->unit_id,
            'sub_unit_id' => $this->sub_unit_id,
            'lesson_id' => $this->lesson_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'teacher' => UserResource::make($this->whenLoaded('teacher')),
            'e_level' => ELevelResource::make($this->whenLoaded('eLevel')),
            'c_level' => CLevelResource::make($this->whenLoaded('cLevel')),
            'course' => CourseResource::make($this->whenLoaded('course')),
            'subject' => SubjectResource::make($this->whenLoaded('subject')),
            'unit' => UnitResource::make($this->whenLoaded('unit')),
            'sub_unit' => SubUnitResource::make($this->whenLoaded('subUnit')),
            'lesson' => LessonResource::make($this->whenLoaded('lesson')),
        ];

        $routeName = $request->route()->getName();
        switch ($routeName) {
            case in_array($routeName , [
                RouteNames::MOBILE_HIERARICHY_RESPONSIBILITIES_BY_TEACHER_ID,
                RouteNames::MOBILE_TEACHER_DETAILS,
                RouteNames::MOBILE_HIERARICHY_TEACHER_DETAILS
            ]):
                $data['e_level'] = ELevelResource::make($this->whenLoaded('publishedELevel'));
                $data['c_level'] = CLevelResource::make($this->whenLoaded('publishedCLevel'));
                $data['course'] = CourseResource::make($this->whenLoaded('publishedCourse'));
                $data['subject'] = SubjectResource::make($this->whenLoaded('publishedSubject'));
                $data['unit'] = UnitResource::make($this->whenLoaded('publishedUnit'));
                $data['sub_unit'] = SubUnitResource::make($this->whenLoaded('publishedSubUnit'));
                $data['lesson'] = LessonResource::make($this->whenLoaded('publishedLesson'));
            break;
        }



        return $data;
    }
}
