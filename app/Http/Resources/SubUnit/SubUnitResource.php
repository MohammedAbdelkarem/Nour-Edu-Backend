<?php

namespace App\Http\Resources\SubUnit;

use App\Enums\LevelEnum;
use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Constants\MediaCollection;
use App\Http\Resources\File\FileResource;
use App\Http\Resources\Quiz\QuizResource;
use App\Http\Resources\Unit\UnitResource;
use App\Http\Resources\Media\MediaResource;
use App\Http\Resources\CLevel\CLevelResource;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\ELevel\ELevelResource;
use App\Http\Resources\Lesson\LessonResource;
use App\Http\Resources\Subject\SubjectResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SubUnitResource extends JsonResource
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
            'subject_id' => $this->subject_id,
            'unit_id' => $this->unit_id,
            'name' => $this->name,
            'bio' => $this->bio,
            'contry_id' => $this->contry_id,
            'priority' => $this->priority,
            'number_of_published_contents' => $this->childsPublishedCounts(),
            'number_of_published_quizzes' => $this->publishedQuizzesCounts(),
            'number_of_published_files' => $this->publishedFilesCounts(),
            'media' => MediaResource::make($this->getFirstMedia(MediaCollection::SUB_UNIT_COLLECTION)),
        ];

        if(auth()->user()->isStudent())
            $data['is_purchased'] = is_purchased($this->id, LevelEnum::SUB_UNIT , auth()->id());

        $data['duration'] = auth()->user()->isAdmin()
            ? duration($this)
            : duration($this, true);

        if(auth()->user()->isAdmin())
            $data['publish_status'] = $this->publish_status;

        $routeName = $request->route()->getName();
        switch ($routeName) 
        {
            case RouteNames::ADMIN_SUB_UNIT_LIST:
                $data['number_of_quizzes'] = $this->quizzesCounts();
                $data['number_of_files'] = $this->filesCounts();
                $data['publish_status'] = $this->publish_status;
                $data['number_of_contents'] = $this->childsCounts();
                $data['created_at'] = $this->created_at;
                $data['updated_at'] = $this->updated_at;
                $data['unit'] = UnitResource::make($this->whenLoaded('unit'));
                $data['lessons'] = LessonResource::collection($this->whenLoaded('lessons'));
            break;
            case RouteNames::MOBILE_HIERARICHY_SUB_UNIT:
                $data['lessons'] = LessonResource::collection($this->whenLoaded('publishedLessons'));
                $data['files'] = FileResource::collection($this->whenLoaded('publishedFiles'));
                $data['quizzes'] = QuizResource::collection($this->whenLoaded('publishedQuizzes'));
            break;
        }

        return $data;
    }
}
