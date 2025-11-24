<?php

namespace App\Http\Resources\Answer;

use App\Constants\RouteNames;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResource extends JsonResource
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
            'text' => $this->text,
            'is_correct' => $this->is_correct,
            'priority' => $this->priority,
            'question_id' => $this->question_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        $routeName = $request->route()->getName();
        switch ($routeName) 
        {
            case RouteNames::MOBILE_QUIZ_PREV_SOLUTION:
                $data['student_answer'] = $this->whenLoaded('studentAnswers');
            break;
        }

        return $data;
    }
}
