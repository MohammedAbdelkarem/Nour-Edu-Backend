<?php

namespace App\Http\Resources;

use App\Constants\RouteNames;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Constants\MediaCollection;
use App\Http\Resources\Media\MediaResource;

class SellPointResource extends JsonResource
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
            'address' => $this->address,
            'phone' => $this->phone,
            'image' => MediaResource::make($this->getFirstMedia(MediaCollection::SELL_POINT_COLLECTION)),
        ];

        $routeName = $request->route()->getName();

        return $data;
    }
}
