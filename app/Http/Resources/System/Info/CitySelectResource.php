<?php

namespace App\Http\Resources\System\Info;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CitySelectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
        ];
    }
}