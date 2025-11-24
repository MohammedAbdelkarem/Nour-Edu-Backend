<?php

namespace App\Http\Resources\Lesson;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'is_pinned' => $this->is_pinned,
            'created_at' => $this->created_at,
            'user' => UserResource::make($this->whenLoaded('user')),
            'is_replayed' => is_replayed($this->id),
            'is_own' => $this->user_id == auth()->id(),
        ];

        $routeName = $request->route()->getName();

        switch ($routeName)
        {
            case in_array($routeName , [
                RouteNames::MOBILE_COMMENTS_LIST,
                RouteNames::MOBILE_TEACHER_LESSONS,
            ]):
                $data['replay'] = ReplayResource::make($this->whenLoaded('existReplay'));
                break;
            case RouteNames::ADMIN_LESSON_SHOW:
                $data['status'] = $this->status;
                $data['replay'] = ReplayResource::make($this->whenLoaded('replay'));
                break;
        }

        return $data;
    }
}
