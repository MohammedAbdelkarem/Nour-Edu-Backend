<?php

namespace App\Http\Resources\Day;

use App\Constants\RouteNames;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DayResource extends JsonResource
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
            'name' => $this->name,
            'created_at' => $this->created_at,
        ];

        $routeName = $request->route()->getName();

        // switch ($routeName)
        // {
        //     case RouteNames::EXAMPLE:
        //         $data['foo']   = $this->bar;
        //     break;
        //     case RouteNames::EXAMPLE:
        //         $data['foo']   = $this->bar;
        //     break;
        // }

        return $data;
    }
}
