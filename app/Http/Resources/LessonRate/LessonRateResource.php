<?php

namespace App\Http\Resources\LessonRate;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonRateResource extends JsonResource
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
            'rate' => $this->rate,
            'student' => UserResource::make($this->whenLoaded('student')),
        ];


        return $data;
    }
}
