<?php

namespace App\Models;

use App\Constants\Resources;
use App\Enums\PublishStatusEnum;
use App\Models\Scopes\QuizResultScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Quiz extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new QuizResultScope);
    }

    /**
     * @return \App\Models\Quiz
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            null,
            Resources::QUIZ,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }

    // Relationships
    public function context()
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'quiz_question', 'quiz_id', 'question_id')
                    ->withPivot('priority')
                    ->withTimestamps();
    }

    public function quizResults(): HasMany
    {
        return $this->hasMany(QuizResult::class, 'quiz_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('publish_status', PublishStatusEnum::PUBLISHED->value);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    public function scopeSearchForMobile($query, $search , $studnet_id = null)
    {
        return $query->published()
            ->where(function($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->whereHas('context', function($query) use ($studnet_id) {
                $query->whereHas('unlockedContexts', function($query) use ($studnet_id) {
                    $query->where('user_id', $studnet_id);
                });
            });
    }

    public function scopeFilterForMobile($query, $data, $student_id)
    {
        return $query->published()
            ->when(isset($data['subject_ids']) || isset($data['unit_ids']) || isset($data['sub_unit_ids']) || isset($data['lesson_ids']), function($query) use ($data) {
                $query->where(function($subQuery) use ($data) {
                    $subQuery->when(isset($data['subject_ids']), function($query) use ($data) {
                        $query->whereIn('context_id', $data['subject_ids'])->where('context_type', Subject::class);
                    })
                    ->when(isset($data['unit_ids']), function($query) use ($data) {
                        $query->orWhereIn('context_id', $data['unit_ids'])->where('context_type', Unit::class);
                    })
                    ->when(isset($data['sub_unit_ids']), function($query) use ($data) {
                        $query->orWhereIn('context_id', $data['sub_unit_ids'])->where('context_type', SubUnit::class);
                    })
                    ->when(isset($data['lesson_ids']), function($query) use ($data) {
                        $query->orWhereIn('context_id', $data['lesson_ids'])->where('context_type', Lesson::class);
                    });
                });
            })
            ->whereHas('context', function($query) use ($student_id) {
                $query->whereHas('unlockedContexts', function($query) use ($student_id) {
                    $query->where('user_id', $student_id);
                });
            });
    }
}
