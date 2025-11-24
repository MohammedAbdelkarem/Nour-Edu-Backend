<?php

namespace App\Http\Resources\System\CustomerServiceCard;

use App\Enums\CustomerServiceCard\CustomerServiceCardStatus;
use App\Http\Resources\Users\Profile\UserSugResource;
use App\Traits\ImagesHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerServiceCardResource extends JsonResource
{
    use ImagesHelper;
    public function toArray(Request $request): array
    {
        $user = auth()->user();
        $data = $this->getCardData();

        if ($user && $user->isAdmin())
            $data += $this->getCardAdminData();

        return $data;
    }

    public function getCardData(): array
    {
        $status = $this->getStatus();
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'description'       => $this->description,
            'date'              => $this->date ? Carbon::parse($this->date)->format('Y-m-d') : null,
            'admin_answer'      => $this->admin_answer ?? "",
            'type'              => __("customer_card.{$this->type}"),
            'type_type'         => $this->type,
            'status'            => __("customer_card.{$status}"), //This for showing in UI
            'status_type'       => $status, //This is used for colors and graphics in frontEnd
            'created_at'        => Carbon::parse($this->created_at)->translatedFormat('Y-m-d g:i A'),
            'user'              => new UserSugResource($this->user),
            'media'             => $this->getMedia(),
        ];
    }

    public function getCardAdminData(): array
    {
        return [
            'in_trash' => (bool) $this->deleted_at,
        ];
    }

    public function getMedia()
    {
        $data = [];
        foreach ($this->media as $med) {
            $data[] = [
                'id'        => $med->id,
                'media_url' => $this->getFullImageUrl($med->media_url),
            ];
        }
        return $data;
    }

    public function getStatus(): string
    {
        return $this->admin_answer
            ? CustomerServiceCardStatus::CLOSED->value
            : CustomerServiceCardStatus::PENDING->value;
    }
}
