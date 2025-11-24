<?php

namespace App\Http\Resources\System\Info;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = auth()->user();
        if ($user && $user->isAdmin())
            return [
                "id" => $this->id,
                "name" => $this->name,
                "country_id" => $this->country_id,
                // "country_name" => $this->country->name,
                // "country_code" => $this->country->country_code,
                "created_at" => Carbon::parse($this->created_at)->translatedFormat("Y-m-d g:i A"),
                "updated_at" => Carbon::parse($this->updated_at)->translatedFormat("Y-m-d g:i A"),
            ];
        return [
            "id" => $this->id,
            "name" => $this->name,
            "country_id" => $this->country_id,
            // "country_name" => $this->country->name,
            // "country_code" => $this->country->country_code,
        ];
    }
}
