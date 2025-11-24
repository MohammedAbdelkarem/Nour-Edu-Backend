<?php

namespace App\Http\Resources\File;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Constants\MediaCollection;
use App\Http\Resources\Media\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
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
            'context_id' => $this->context_id,
            'context_type' => getModelName($this->context_type),
            'priority' => $this->priority,
            'file' => MediaResource::make($this->getFirstMedia(MediaCollection::FILE_COLLECTION)),
        ];

        $routeName = $request->route()->getName();
        switch ($routeName) 
        {
            case RouteNames::ADMIN_FILE_LIST:
                $data['publish_status'] = $this->publish_status;
                $data['created_at'] = $this->created_at;
                $data['updated_at'] = $this->updated_at;
            break;
        }

        return $data;
    }
}
