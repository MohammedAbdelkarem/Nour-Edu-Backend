<?php

namespace App\Http\Resources\ELevel;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Constants\MediaCollection;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Media\MediaResource;
use App\Http\Resources\CLevel\CLevelResource;
use App\Http\Resources\Teacher\TeacherResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Responsibility\ResponsibilityResource;

class ELevelResource extends JsonResource
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
            'name' => $this->name,
            'bio' => $this->bio,
            'contry_id' => $this->contry_id,
            'number_of_published_contents' => $this->childsPublishedCounts(),
            'media' => MediaResource::make($this->getFirstMedia(MediaCollection::E_LEVEL_COLLECTION)),
            'teachers' => $this->relationLoaded('responsibilities') 
                ? UserResource::collection($this->responsibilities->pluck('teacher')->unique('id')->values())
                : [],
        ];

        $data['duration'] = optional(auth()->user())->isAdmin()
        ? duration($this)
        : duration($this, true);


        $routeName = $request->route()->getName();
        switch ($routeName) 
        {
            case RouteNames::ADMIN_E_LEVEL_LIST:
                $data['publish_status'] = $this->publish_status;
                $data['number_of_contents'] = $this->childsCounts();
                $data['created_at'] = $this->created_at;
                $data['updated_at'] = $this->updated_at;
                // $data['responsibilities'] = ResponsibilityResource::collection($this->whenLoaded('responsibilities'));
                // $data['c_levels'] = CLevelResource::collection($this->whenLoaded('cLevels'));
            break;
        }

        return $data;
    }
} 