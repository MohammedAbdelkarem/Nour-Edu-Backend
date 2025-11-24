<?php

namespace App\Http\Resources\LessonQuestion;

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use App\Constants\MediaCollection;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Media\MediaResource;
use App\Http\Resources\Lesson\LessonResource;
use App\Http\Resources\Teacher\TeacherResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonQuestionResource extends JsonResource
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
            'question' => $this->question,
            'answer' => $this->answer,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'question_images' => MediaResource::collection($this->getMedia(MediaCollection::LESSON_QUESTION_COLLECTION)),
            'answer_images' => MediaResource::collection($this->getMedia(MediaCollection::LESSON_QUESTION_ANSWER_COLLECTION)),
            'teacher' => TeacherResource::make($this->whenLoaded('teacher')),
            'student' => UserResource::make($this->whenLoaded('student')),
            'lesson' => LessonResource::make($this->whenLoaded('lesson')),
        ];

        $routeName = $request->route()->getName();


        return $data;
    }
}
