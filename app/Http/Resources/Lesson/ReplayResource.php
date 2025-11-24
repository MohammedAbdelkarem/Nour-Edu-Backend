<?php

namespace App\Http\Resources\Lesson;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ReplayResource extends JsonResource
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
            'text' => $this->text,
            'created_at' => $this->created_at,
            'user' => UserResource::make($this->whenLoaded('user')),
            'is_own' => $this->user_id == auth()->id(),
        ];

        $routeName = $request->route()->getName();
        switch ($routeName)
        {
            case RouteNames::ADMIN_LESSON_SHOW:
                $data['status'] = $this->status;
                break;
        }

        return $data;
    }
}
