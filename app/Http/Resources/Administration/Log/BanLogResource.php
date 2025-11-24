<?php

namespace App\Http\Resources\Administration\Log;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Constants\MediaCollection;
use App\Http\Resources\Media\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BanLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $banned = $this->bannedUser;
        $bannedBy = $this->banningUser;
        $unbannedBy = $this->unbanningUser;
        $reason = $this->reason;
        $unbanReason = $this->unban_reason;

        return [
            "id"                => $this->id,
            "reason"            => $reason,
            "unban_reason"      => $unbanReason ?? "",
            "banned_id"         => $this->banned_id,
            "banned_name"       => $banned->name,
            "banned_img"        => MediaResource::make($banned->getFirstMedia(MediaCollection::USER_COLLECTION)),
            "banned_by_id"      => $this->banned_by_id,
            "banned_by_name"    => $bannedBy->name,
            "banned_by_img"     => MediaResource::make($bannedBy->getFirstMedia(MediaCollection::USER_COLLECTION)),
            "unbanned_by_id"    => $this->unbanned_by_id,
            "unbanned_by_name"  => $unbannedBy?->name ?? "",
            "unbanned_by_img"   => MediaResource::make($unbannedBy->getFirstMedia(MediaCollection::USER_COLLECTION)),
            "created_at"        => Carbon::parse($this->created_at)->translatedFormat('Y-m-d g:i A'),
            "banned_until"      => Carbon::parse($this->banned_until)->format("Y-m-d H:i"),
        ];
    }
}