<?php

namespace App\Http\Resources\Question;

use App\Enums\LevelEnum;
use Illuminate\Http\Request;
use App\Constants\MediaCollection;
use App\Http\Resources\Unit\UnitResource;
use App\Http\Resources\Media\MediaResource;
use App\Http\Resources\Answer\AnswerResource;
use App\Http\Resources\SubUnit\SubUnitResource;
use App\Http\Resources\Lesson\LessonResource;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'e_level_id' => $this->e_level_id,
            'c_level_id' => $this->c_level_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'unit_id' => $this->unit_id,
            'sub_unit_id' => $this->sub_unit_id,
            'lesson_id' => $this->lesson_id,
            'text' => $this->text,
            'hint' => $this->hint,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'lesson' => $this->whenLoaded('lesson'),
            // 'unit' => UnitResource::make($this->whenLoaded('unit')),
            // 'sub_unit' => SubUnitResource::make($this->whenLoaded('subUnit')),
            'answers' => AnswerResource::collection($this->whenLoaded('answers')),
            'media' => MediaResource::make($this->getFirstMedia(MediaCollection::QUESTION_COLLECTION)),
        ];

        if(auth()->user()->isStudent())
            $data['is_saved'] = is_saved($this->id, LevelEnum::QUESTION , auth()->id());

        return $data;
    }
}
