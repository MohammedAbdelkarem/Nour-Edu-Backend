<?php

namespace App\Http\Resources\Media;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'url'   => $this->getUrl(),
            'type'  => getMediaType($this->mime_type),
            'quality'  => $this->getCustomProperty('quality') ?? null,
        ];
    }
}
