<?php

namespace App\Http\Resources\System\Info;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = auth()->user();
        if ($user && $user->isAdmin()) {
            return [
                "id"                => $this->id,
                "name"              => $this->name,
                "app_key"           => $this->app,
                "updated_by_id"     => $this->updated_by,
                "updated_by_name"   => $this->updater ? $this->updater->name : "",
                "created_at"        => Carbon::parse($this->created_at)->translatedFormat("Y-m-d g:i A"),
                "updated_at"        => Carbon::parse($this->updated_at)->translatedFormat("Y-m-d g:i A"),
                "faqs"              => FAQResource::collection($this->faqs),
            ];
        }
        return [
            "id"    => $this->id,
            "name"  => $this->name,
            "faqs"  => FAQResource::collection($this->faqs),
        ];
    }
}
