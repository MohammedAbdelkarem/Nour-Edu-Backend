<?php

namespace App\Http\Resources\Context;

use App\Constants\RouteNames;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnlockedContextResource extends JsonResource
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
            'context_id' => $this->context_id,
            'context_type' => getModelName($this->context_type),
        ];

        $routeName = $request->route()->getName();


        return $data;
    }
}
