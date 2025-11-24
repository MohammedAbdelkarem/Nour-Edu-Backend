<?php

namespace App\Http\Resources\Media;

use App\Constants\RouteNames;
use App\Enums\MediaTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DefaultMediaResource extends JsonResource
{
    //for lesson video , coming soon only
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => 0,
            'url'   => config('app.url') . '/' . config('_custom.lesson_default_video'),
            'type'  => MediaTypeEnum::VIDEO,
            'quality'  => "720"
        ];
    }
}
