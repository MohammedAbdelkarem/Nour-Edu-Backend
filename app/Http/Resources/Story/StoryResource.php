<?php

namespace App\Http\Resources\Story;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Constants\MediaCollection;
use App\Http\Resources\Media\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'storiable_id' => $this->storiable_id,
            'storiable_type' => getModelName($this->storiable_type),
            'external_link' => $this->external_link,
            'media' => MediaResource::collection($this->getMedia(MediaCollection::STORY_COLLECTION)),
            
        ];

        $routeName = $request->route()->getName();

        if(auth()->user()->isAdmin())
            $data['storiable'] = $this->whenLoaded('storiable');

        switch ($routeName) 
        {
            case RouteNames::ADMIN_STORY_GET:
                $data['ended_at'] = $this->end_at;
                // $data['status'] = $this->status;
            break;
        }

        return $data;
    }
}
