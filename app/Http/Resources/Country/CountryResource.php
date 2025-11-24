<?php

namespace App\Http\Resources\Country;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Constants\MediaCollection;
use App\Http\Resources\Media\MediaResource;
use App\Http\Resources\System\Info\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'country_code' => $this->country_code,
            'media' => MediaResource::make($this->getFirstMedia(MediaCollection::CONTRY_FLAG_COLLECTION)),
            'cities' => CityResource::collection($this->cities),
        ];

        return $data;
    }
}
