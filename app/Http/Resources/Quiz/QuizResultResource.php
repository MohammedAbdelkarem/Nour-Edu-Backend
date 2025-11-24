<?php

namespace App\Http\Resources\Quiz;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Question\QuestionResource;

class QuizResultResource extends JsonResource
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
            'quiz_id' => $this->quiz_id,
            'student_id' => $this->student_id,
            'degree' => $this->degree,
            'result' => $this->result,
            'number_of_correct_answers' => $this->number_of_correct_answers,
            'number_of_wrong_answers' => $this->number_of_wrong_answers,
            'number_of_answered_questions' => $this->number_of_answered_questions,
            'taken_period' => $this->taken_period,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        $routeName = $request->route()->getName();
        switch ($routeName) 
        {
            case RouteNames::MOBILE_QUIZ_PREV_SOLUTION:
                $data['quiz'] = QuizResource::make($this->whenLoaded('quiz'));
            break;
        }


        return $data;
    }
}
