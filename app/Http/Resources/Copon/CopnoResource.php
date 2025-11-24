<?php

namespace App\Http\Resources\Copon;

use App\Constants\RouteNames;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CopnoResource extends JsonResource
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
            'coupon' => $this->coupon,
            'amount' => $this->amount,
            'type' => $this->type,
            'number_of_uses' => $this->number_of_uses,
            'is_expired' => $this->is_expired,
            'context_id' => $this->context_id,
            'context_type' => getModelName($this->context_type),
            'expired_at' => $this->expired_at,
            'user_id' => $this->user_id,
            'number_of_max_uses' => $this->number_of_max_uses,
            'context_expired_at' => $this->context_expired_at,
            'used_at' => $this->used_at,
        ];

        $routeName = $request->route()->getName();


        return $data;
    }
}
