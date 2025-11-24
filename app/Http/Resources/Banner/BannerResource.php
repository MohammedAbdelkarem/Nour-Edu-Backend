<?php

namespace App\Http\Resources\Banner;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Constants\MediaCollection;
use App\Http\Resources\Media\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
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
            'bannerable_id' => $this->bannerable_id,
            'bannerable_type' => getModelName($this->bannerable_type),
            'external_link' => $this->external_link,
            'media' => MediaResource::collection($this->getMedia(MediaCollection::BANNER_COLLECTION)),
        ];

        if(auth()->user()->isAdmin())
            $data['bannerable'] = $this->whenLoaded('bannerable');


        $routeName = $request->route()->getName();

        switch ($routeName) 
        {
            case RouteNames::ADMIN_BANNER_GET:
                $data['created_at'] = $this->created_at;
                // $data['status'] = $this->status;
            break;
        }

        return $data;
    }
}
