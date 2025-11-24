<?php

namespace App\Http\Resources\Subject;

use App\Enums\LevelEnum;
use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Constants\MediaCollection;
use App\Http\Resources\File\FileResource;
use App\Http\Resources\Quiz\QuizResource;
use App\Http\Resources\Unit\UnitResource;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Media\MediaResource;
use App\Http\Resources\CLevel\CLevelResource;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\ELevel\ELevelResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Responsibility\ResponsibilityResource;
use App\Http\Resources\Teacher\TeacherResource;

class SubjectResource extends JsonResource
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
            'e_level_id' => $this->e_level_id,
            'c_level_id' => $this->c_level_id,
            'course_id' => $this->course_id,
            'name' => $this->name,
            'bio' => $this->bio,
            'contry_id' => $this->contry_id,
            'number_of_published_contents' => $this->childsPublishedCounts(),
            'price' => $this->price,
            'access_type' => $this->access_type,
            'number_of_published_lessons' => $this->numberOfPublishedLessons() ?? 0,
            'number_of_published_quizzes' => $this->publishedQuizzesCounts(),
            'number_of_published_files' => $this->publishedFilesCounts(),
            'media' => MediaResource::make($this->getFirstMedia(MediaCollection::SUBJECT_COLLECTION)),
            'icon' => MediaResource::make($this->getFirstMedia(MediaCollection::SUBJECT_ICON_COLLECTION)),
            'video' => MediaResource::make($this->getFirstMedia(MediaCollection::SUBJECT_VIDEO_COLLECTION)),
            'teachers' => $this->relationLoaded('responsibilities') 
                ? UserResource::collection($this->responsibilities->pluck('teacher')->unique('id')->values())
                : [],
        ];

        if(auth()->user()->isStudent())
            $data['is_purchased'] = is_purchased($this->id, LevelEnum::SUBJECT , auth()->id());

        $data['duration'] = auth()->user()->isAdmin()
        ? duration($this)
        : duration($this, true);

        $routeName = $request->route()->getName();

        switch ($routeName) 
        {
            case RouteNames::ADMIN_SUBJECT_LIST:
                $data['number_of_quizzes'] = $this->quizzesCounts();
                $data['number_of_files'] = $this->filesCounts();
                $data['number_of_purchased_students'] = $this->number_of_purchased_students;
                $data['publish_status'] = $this->publish_status;
                $data['number_of_contents'] = $this->childsCounts();
                $data['created_at'] = $this->created_at;
                $data['updated_at'] = $this->updated_at;
                // $data['responsibilities'] = ResponsibilityResource::collection($this->whenLoaded('responsibilities'));
                $data['course'] = CourseResource::make($this->whenLoaded('course'));
                $data['units'] = UnitResource::collection($this->whenLoaded('units'));
            break;
            case RouteNames::MOBILE_PURCHASED_SUBJECTS:
                $data['units'] = UnitResource::collection($this->whenLoaded('publishedUnits'));
            break;
            case RouteNames::MOBILE_HIERARICHY_SUBJECT:
                $data['teachers'] = $this->relationLoaded('responsibilities') 
                    ? UserResource::collection($this->responsibilities->pluck('teacher')->unique('id')->values())
                    : [];
                $data['units'] = UnitResource::collection($this->whenLoaded('publishedUnits'));
                $data['files'] = FileResource::collection($this->whenLoaded('publishedFiles'));
                $data['quizzes'] = QuizResource::collection($this->whenLoaded('publishedQuizzes'));
            break;
            case RouteNames::MOBILE_HOME_SEARCH:
                $data['course'] = CourseResource::make($this->whenLoaded('course'));
            break;
        }

        return $data;
    }
}
