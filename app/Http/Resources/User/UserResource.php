<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Constants\MediaCollection;
use App\Http\Resources\Media\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Responsibility\ResponsibilityResource;

class UserResource extends JsonResource
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
            'image' => MediaResource::make($this->getFirstMedia(MediaCollection::USER_COLLECTION)),
            'role_id' => $this->role_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        $routeName = $request->route()->getName();

        if(auth()->user()->isStudent() || auth()->user()->isAdmin())
            $data['balance'] = $this->balance;

        switch ($routeName) 
        {
            case RouteNames::ADMIN_TEACHER_LIST:
                $data['bio'] = $this->bio;
                $data['responsibilities'] = ResponsibilityResource::collection($this->whenLoaded('responsibilities'));
            break;
            case in_array($routeName , [RouteNames::STUDENT_HOME, RouteNames::MOBILE_PROGRESS_GET]):
                $data['c_level_name'] = $this->whenLoaded('c_level')->name ?? null  ;
                $data['e_level_name'] = $this->whenLoaded('e_level')->name ?? null;
            break;
            case RouteNames::ADMIN_STUDENT_PROFILE:
                $data['profile'] = $this->whenLoaded('profile');
                $data['city'] = $this->whenLoaded('city');
                $data['e_level'] = $this->whenLoaded('e_level');
                $data['c_level'] = $this->whenLoaded('c_level');
                $data['parent'] = $this->whenLoaded('parent');
                $data['transactions'] = $this->whenLoaded('transactions');
        }

        return $data;
    }
}
