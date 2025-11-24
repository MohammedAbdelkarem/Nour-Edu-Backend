<?php

namespace App\Http\Resources\CLevel;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Constants\MediaCollection;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Media\MediaResource;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\ELevel\ELevelResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Responsibility\ResponsibilityResource;

class CLevelResource extends JsonResource
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
            'name' => $this->name,
            'bio' => $this->bio,
            'number_of_published_contents' => $this->childsPublishedCounts(),
            'media' => MediaResource::make($this->getFirstMedia(MediaCollection::C_LEVEL_COLLECTION)),
            'teachers' => $this->relationLoaded('responsibilities') 
                ? UserResource::collection($this->responsibilities->pluck('teacher')->unique('id')->values())
                : [],
        ];

        $data['duration'] = auth()->user()->isAdmin()
            ? duration($this)
            : duration($this, true);

        $routeName = $request->route()->getName();
        switch ($routeName) 
        {
            case RouteNames::ADMIN_C_LEVEL_LIST:
                $data['number_of_lessons'] = $this->lessonsCounts();
                $data['publish_status'] = $this->publish_status;
                $data['number_of_contents'] = $this->childsCounts();
                $data['created_at'] = $this->created_at;
                $data['updated_at'] = $this->updated_at;
                // $data['responsibilities'] = ResponsibilityResource::collection($this->whenLoaded('responsibilities'));
                $data['e_level'] = ELevelResource::make($this->whenLoaded('eLevel'));
                $data['courses'] = CourseResource::collection($this->whenLoaded('courses'));
            break;
        }

        return $data;
    }
}
