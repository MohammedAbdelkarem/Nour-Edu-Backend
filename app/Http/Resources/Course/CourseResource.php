<?php

namespace App\Http\Resources\Course;

use App\Enums\LevelEnum;
use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Constants\MediaCollection;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Media\MediaResource;
use App\Http\Resources\CLevel\CLevelResource;
use App\Http\Resources\ELevel\ELevelResource;
use App\Http\Resources\Subject\SubjectResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Responsibility\ResponsibilityResource;

class CourseResource extends JsonResource
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
            'name' => $this->name,
            'bio' => $this->bio,
            'number_of_published_contents' => $this->childsPublishedCounts(),
            'price' => $this->price,
            'access_type' => $this->access_type,
            'media' => MediaResource::make($this->getFirstMedia(MediaCollection::COURSE_COLLECTION)),
            'icon' => MediaResource::make($this->getFirstMedia(MediaCollection::COURSE_ICON_COLLECTION)),
            'teachers' => $this->relationLoaded('responsibilities') 
                ? UserResource::collection($this->responsibilities->pluck('teacher')->unique('id')->values())
                : [],
        ];

        if(auth()->user()->isStudent())
            $data['is_purchased'] = is_purchased($this->id, LevelEnum::COURSE , auth()->id());

        $data['duration'] = auth()->user()->isAdmin()
        ? duration($this)
        : duration($this, true);

        $routeName = $request->route()->getName();

        switch ($routeName) 
        {
            case RouteNames::ADMIN_COURSE_LIST:
                $data['publish_status'] = $this->publish_status;
                $data['number_of_contents'] = $this->childsCounts();
                $data['number_of_purchased_students'] = $this->number_of_purchased_students;
                $data['created_at'] = $this->created_at;
                $data['updated_at'] = $this->updated_at;
                // $data['responsibilities'] = ResponsibilityResource::collection($this->whenLoaded('responsibilities'));
                $data['c_level'] = CLevelResource::make($this->whenLoaded('cLevel'));
                $data['subjects'] = SubjectResource::collection($this->whenLoaded('subjects'));
            break;
            case RouteNames::MOBILE_PURCHASED_COURSES:
                $data['subjects'] = SubjectResource::collection($this->whenLoaded('publishedSubjects'));
            break;
            case RouteNames::STUDENT_HOME:
                $data['subjects'] = SubjectResource::collection($this->whenLoaded('publishedSubjects'));
            break;
        }

        return $data;
    }
}
