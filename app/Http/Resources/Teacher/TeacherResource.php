<?php

namespace App\Http\Resources\Teacher;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Constants\MediaCollection;
use App\Http\Resources\Media\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Responsibility\ResponsibilityResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data =  [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'is_male' => $this->is_male,
            'birth_date' => $this->birth_date,
            'image' => MediaResource::make($this->getFirstMedia(MediaCollection::USER_COLLECTION)),
            'role_id' => $this->role_id,
            'e_level' => $this->whenLoaded('e_level')->name ?? null,
            'c_level' => $this->whenLoaded('c_level')->name ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        $routeName = $request->route()->getName();

        switch ($routeName) 
        {
            case in_array($routeName , [
                RouteNames::MOBILE_HIERARICHY_TEACHER_DETAILS,
                RouteNames::ADMIN_TEACHER_LIST,
                RouteNames::MOBILE_TEACHER_HOME
            ]):
                $data['bio'] = $this->bio;
                $data['responsibilities'] = ResponsibilityResource::collection($this->whenLoaded('responsibilities'));
            break;
            case RouteNames::MOBILE_TEACHER_DETAILS:
                $data['bio'] = $this->bio;
            break;
        }

        return $data;
    }
}
