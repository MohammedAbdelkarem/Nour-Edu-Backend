<?php

namespace App\Services\Quiz;

use App\Models\Quiz;
use App\Services\MainService;
use App\Services\Base\ContextService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuizService extends MainService
{
    public function __construct(
        protected ContextService $contextService,
    ) {}

    public function getAll($data)
    {
        $query = Quiz::query();
        $query->with(['context', 'createdBy', 'questions']);
        // Filter by context ID if provided
        if (isset($data['context_id'])) {
            $query->where('context_id', $data['context_id']);
        }

        // Filter by context type if provided
        if (isset($data['context_type'])) {
            $data['context_type'] = getModel($data['context_type']);
            
            $query->where('context_type', $data['context_type']);
        }
        
        return getOrPaginate(
            $query->orderBy('priority', 'asc'),
            $data
        );
    }

    public function show($id)
    {
        return Quiz::findByIdOrFail($id, ['context', 'createdBy', 'questions.answers']);
    }

    public function store($validatedData)
    {
        $validatedData['context_type'] = getModel($validatedData['context_type']);
        $validatedData['created_by'] = auth()->id();

        $quiz = Quiz::create($validatedData);

        $quiz->number_of_questions = count($validatedData['questions']);

        $quiz->one_question_degree = $validatedData['degree'] / count($validatedData['questions']);

        foreach ($validatedData['questions'] as $question) {
            $questionData[$question['id']] = ['priority' => $question['priority']];
        }

        $quiz->questions()->attach($questionData);

        $quiz->save();

        // $this->contextService->updateParentNumberOfQuizzes($quiz, $quiz->context_id, '+');
    }

    public function detachQuestionsFromQuiz($quizId, $questionIds)
    {
        $quiz = Quiz::findByIdOrFail($quizId);

        $this->contextService->checkIfQuizHasResults($quizId);

        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($quizId , Quiz::class);

        $quiz->questions()->detach($questionIds);

        $quiz->number_of_questions -= count($questionIds);

        $quiz->one_question_degree = $quiz->degree / $quiz->number_of_questions;

        $quiz->save();
    }

    public function attachQuestionsToQuiz($quizId, $questionIds)
    {
        $quiz = Quiz::findByIdOrFail($quizId);

        $this->contextService->checkIfQuizHasResults($quizId);

        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($quizId , Quiz::class);

        $quiz->questions()->attach($questionIds);

        $quiz->number_of_questions += count($questionIds);

        $quiz->one_question_degree = $quiz->degree / $quiz->number_of_questions;

        $quiz->save();
    }

    public function update($validatedData, $id)
    {
        $quiz = Quiz::findByIdOrFail($id);

        $this->contextService->checkIfQuizHasResults($id);

        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , Quiz::class);

        $quiz->update($validatedData);

        // foreach ($validatedData['questions'] as $question) {
        //     $questionData[$question['id']] = ['priority' => $question['priority']];
        // }
        
        // $quiz->questions()->sync($questionData);

        // $quiz->number_of_questions = count($validatedData['questions']);

        $quiz->save();
    }

    public function destroy($id)
    {
        $quiz = Quiz::findByIdOrFail($id);

        $this->contextService->checkIfQuizHasResults($id);
        
        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , Quiz::class);

        // Update parent context quiz count before deletion
        // $this->contextService->updateParentNumberOfQuizzes($quiz, $quiz->context_id, '-');
        
        $quiz->delete();
    }

    public function changePublishStatus($id, $status)
    {
        $quiz = Quiz::findByIdOrFail($id);
        
        $this->contextService->changePublishStatus($quiz, 'quiz', $status);
    }

    public function changePriority($contextsData)
    {
        $this->contextService->changeContextsPriority($contextsData, Quiz::class);
    }

    public function search($data , $student_id)
    {
        $quizzes = Quiz::searchForMobile($data['search'] , $student_id);

        return getOrPaginate(
            $quizzes,
            $data
        );
    }

    public function filter($data , $student_id)
    {
        $quizzes = Quiz::filterForMobile($data, $student_id);

        return getOrPaginate(
            $quizzes,
            $data
        );
    }

    public function getPurchasedQuizzes($student_id , $context_type , $data)
    {
        $model = getModel($context_type);
        
        $quizzes = Quiz::published()->whereHas('context', function($query) use ($student_id , $model) {
            $query->whereHas('unlockedContexts', function($query) use ($student_id) {
                $query->where('user_id', $student_id);
            })
            ->where('context_type', $model);
        });

        return getOrPaginate(
            $quizzes,
            $data
        );
    }
}
