<?php

namespace App\Http\Resources\Administration\Profile;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Constants\MediaCollection;
use App\Http\Resources\Media\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        
        return [
            "is_me"         => $this->id == auth()->id(),
            "id"            => $this->id,
            "name"          => $this->name,
            "role_id"       => $this->role_id,
            "role_name"     => $this->role->name,
            "birth_date"    => $this->birth_date ?? "",
            "is_male"       => !is_null($this->is_male) ? (bool) $this->is_male : null,
            "email"         => $this->email ?? "",
            "phone_number"  => $this->phone_number,
            "city_id"       => $this->city_id,
            "city_name"     => $this->city["name_" . app()->getLocale()] ?? "",
            "avatar" => MediaResource::make($this->getFirstMedia(MediaCollection::USER_COLLECTION)),
            "active_notifications" => (bool) $this->active_notifications,
            "deactive_at"   => $this->deactive_at ? Carbon::parse($this->deactive_at)->translatedFormat("Y-m-d g:i a") : "",
            "is_active"     => (bool) !$this->deactive_at,
            "created_at"    => Carbon::parse($this->created_at)->translatedFormat("Y-m-d g:i a"),
            "updated_at"    => Carbon::parse($this->updated_at)->translatedFormat("Y-m-d g:i a"),
            // "created_by"    => new AdminListResource($this->adminProfile->creator),
        ];
    }
}
