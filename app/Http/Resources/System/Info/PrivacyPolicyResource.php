<?php

namespace App\Http\Resources\System\Info;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivacyPolicyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = auth()->user();
        if ($user && $user->isAdmin()) {
            $updater = $this->update_by ? $this->updated_by : null;
            return [
                "id" => $this->id,
                "text" => $this->text,
                "lang" => $this->lang,
                "updated_by_id" => $this->update_by,
                "updated_by_name" => $updater ? $updater->name : "",
                "created_at" => Carbon::parse($this->created_at)->translatedFormat("Y-m-d g:i A"),
                "updated_at" => Carbon::parse($this->updated_at)->translatedFormat("Y-m-d g:i A"),
            ];
        }
        return [
            "id" => $this->id,
            "text" => $this->text,
        ];
    }
}
