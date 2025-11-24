<?php

namespace App\Http\Resources\System\Info;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactUsResource extends JsonResource
{
    public function toArray($request)
    {
        $user = auth()->user();
        $data = [
            "id"        => $this->id,
            "link"      => $this->link,
            "type"      => $this->type,
        ];

        if ($user && $user->isAdmin()) {
            $creator = $this->created_by ? $this->creator : null;
            $data += [
                "created_by_id" => $this->created_by,
                "created_by_name" => $creator ? $creator->name : "",
                "created_at" => Carbon::parse($this->created_at)->translatedFormat("Y-m-d g:i A"),
                "updated_at" => Carbon::parse($this->updated_at)->translatedFormat("Y-m-d g:i A"),
            ];
        }
        return $data;
    }
}
