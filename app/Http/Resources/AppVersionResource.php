<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Constants\MediaCollection;
use App\Http\Resources\Media\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AppVersionResource extends JsonResource
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
            'version' => $this->version,
            'url' => $this->url,
            'is_force_update' => $this->is_force_update,
            'app_type' => $this->app_type,
            'description' => $this->description,
            'file' => MediaResource::make($this->getFirstMedia(MediaCollection::APP_VERSION_COLLECTION)),
        ];

        $routeName = $request->route()->getName();

        return $data;
    }
}
