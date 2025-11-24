<?php

namespace App\Http\Resources\Quiz;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Question\QuestionResource;

class QuizResource extends JsonResource
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
            'title' => $this->title,
            'context_id' => $this->context_id,
            'context_type' => getmodelname($this->context_type),
            'period' => $this->period,
            'degree' => $this->degree,
            'pass_degree' => $this->pass_degree,
            'number_of_questions' => $this->number_of_questions,
            'one_question_degree' => $this->one_question_degree,
            'priority' => $this->priority,
        ];

        if(auth()->user()->isStudent())
        {
            $data['is_solved'] = is_solved($this->id, auth()->id());
            $data['quiz_result'] = QuizResultResource::collection($this->whenLoaded('quizResults'));
        }

        $routeName = $request->route()->getName();
        switch ($routeName) 
        {
            case in_array($routeName ,[
                RouteNames::ADMIN_QUIZ_SHOW,
                RouteNames::ADMIN_QUIZ_LIST,
            ]):
                $data['publish_status'] = $this->publish_status;
                $data['created_at'] = $this->created_at;
                $data['updated_at'] = $this->updated_at;
                $data['created_by'] = UserResource::make($this->whenLoaded('createdBy'));
                $data['questions'] = QuestionResource::collection($this->whenLoaded('questions'));
            break;
            case in_array($routeName ,[
                RouteNames::MOBILE_QUIZ_PREV_SOLUTION,
                RouteNames::MOBILE_QUIZ_DETAILS,
            ]):
                $data['questions'] = QuestionResource::collection($this->whenLoaded('questions'));
            break;
        }


        return $data;
    }
}
