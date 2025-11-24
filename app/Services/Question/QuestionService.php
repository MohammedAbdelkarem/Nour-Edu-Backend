<?php

namespace App\Services\Question;

use App\Models\Answer;
use App\Models\Lesson;
use App\Models\Question;
use App\Services\MainService;
use App\Enums\QuestionTypeEnum;
use App\Constants\MediaCollection;
use Illuminate\Support\Facades\DB;
use App\Constants\ExceptionMessages;
use App\Services\Base\ContextService;

class QuestionService extends MainService
{
    public function __construct(
        protected ContextService $contextService,
    ) {}

    public function getAll($data)
    {
        $query = Question::orderBy('created_at', 'desc')
                ->with(['lesson', 'answers']);

        if (isset($data['e_level_ids'])) {
            $query->whereIn('e_level_id', $data['e_level_ids']);
        }
        if (isset($data['c_level_ids'])) {
            $query->whereIn('c_level_id', $data['c_level_ids']);
        }
        if (isset($data['course_ids'])) {
            $query->whereIn('course_id', $data['course_ids']);
        }
        if (isset($data['subject_ids'])) {
            $query->whereIn('subject_id', $data['subject_ids']);
        }
        if (isset($data['unit_ids'])) {
            $query->whereIn('unit_id', $data['unit_ids']);
        }

        if (isset($data['sub_unit_ids'])) {
            $query->whereIn('sub_unit_id', $data['sub_unit_ids']);
        }
        if (isset($data['lesson_ids'])) {
            $query->whereIn('lesson_id', $data['lesson_ids']);
        }

        if (isset($data['type'])) {
            $query->where('type', $data['type']);
        }

        return getOrPaginate($query, $data);
    }

    public function show($id)
    {
        return Question::findByIdOrFail($id, ['answers' , 'lesson']);
    }

    public function store($validatedData)
    {
        $this->chackeQuestionCorrectAnswersCount($validatedData);

        $lesson = Lesson::findByIdOrFail($validatedData['lesson_id']);
            
        $question = Question::create([
            'e_level_id' => $lesson->e_level_id,
            'c_level_id' => $lesson->c_level_id,
            'course_id' => $lesson->course_id,
            'subject_id' => $lesson->subject_id,
            'unit_id' => $lesson->unit_id,
            'sub_unit_id' => $lesson->sub_unit_id,
            'lesson_id' => $validatedData['lesson_id'],
            'text' => $validatedData['text'],
            'hint' => $validatedData['hint'] ?? null,
            'type' => $validatedData['type'],
        ]);

        $question->answers()->createMany($validatedData['answers']);

        if(isset($validatedData['image'])) {
            uploadFileOnMedia($validatedData['image'], $question, MediaCollection::QUESTION_COLLECTION);
        }
    }

    public function update($validatedData, $id)
    {
        $this->chackeQuestionCorrectAnswersCount($validatedData);

        $question = Question::findByIdOrFail($id);

        $this->contextService->checkIfQuestionBelongsToQuizBeforeDeletingOrUpdating($question);

        $question->update($validatedData);

        $question->answers()->delete();

        $question->answers()->createMany($validatedData['answers']);
    }

    public function destroy($id)
    {
        $question = Question::findByIdOrFail($id);
        
        $this->contextService->checkIfQuestionBelongsToQuizBeforeDeletingOrUpdating($question);

        $question->delete();
    }

    private function chackeQuestionCorrectAnswersCount($validatedData)
    {
        if($validatedData['type'] == QuestionTypeEnum::ONE_SELECT->value) {
            $correctAnswers = collect($validatedData['answers'])->where('is_correct', true)->count();
            if($correctAnswers != 1) {
                return unprocessableFailure([], ExceptionMessages::MSG_QUESTION_ANSWERS_IS_CORRECT_ONLY_ONE);
            }
        }
        else {
            $correctAnswers = collect($validatedData['answers'])->where('is_correct', true)->count();
           
            if($correctAnswers <= 1) {
                return unprocessableFailure([], ExceptionMessages::MSG_QUESTION_ANSWERS_IS_CORRECT_MORE_THAN_ONE);
            }
        }
    }
}
