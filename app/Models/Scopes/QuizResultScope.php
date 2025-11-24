<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class QuizResultScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $studentId = auth()->id() ?? null;
        
        $builder->with(['quizResults' => function ($query) use ($studentId) {
            $query->when($studentId != null, function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            });
        }]);
    }
}
