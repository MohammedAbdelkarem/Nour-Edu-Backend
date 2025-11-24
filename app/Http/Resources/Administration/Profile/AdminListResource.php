<?php

namespace App\Http\Resources\Administration\Profile;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Constants\MediaCollection;
use App\Http\Resources\Media\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"        => $this->id,
            "name"      => $this->name,
            "role_name" => $this->role->name,
            "avatar" => MediaResource::make($this->getFirstMedia(MediaCollection::USER_COLLECTION)),
            "email"         => $this->email,
            "is_active"     => (bool) !$this->deactive_at,
            "created_at"    => Carbon::parse($this->created_at)->translatedFormat("Y-m-d g:i a"),
        ];
    }
}
