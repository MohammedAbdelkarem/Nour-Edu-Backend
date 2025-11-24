<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Constants\Resources;
use App\Models\SavedContext;
use App\Constants\MediaCollection;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\InteractsWithMedia;

class Question extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $guarded = [
        'id'
    ];

    /**
     * @return \App\Models\Question
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            GenderEnum::FEMALE,
            Resources::QUESTION,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::QUESTION_COLLECTION)->singleFile();
    }

    public function delete()
    {
        deleteFilesFromMedia($this, MediaCollection::QUESTION_COLLECTION);
        return parent::delete();
    }

    // Relationships
    public function eLevel(): BelongsTo
    {
        return $this->belongsTo(ELevel::class, 'e_level_id');
    }

    public function cLevel(): BelongsTo
    {
        return $this->belongsTo(CLevel::class, 'c_level_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function subUnit(): BelongsTo
    {
        return $this->belongsTo(SubUnit::class, 'sub_unit_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'question_id');
    }

    public function quizzes(): BelongsToMany
    {
        return $this->belongsToMany(Quiz::class, 'quiz_question', 'question_id', 'quiz_id')
                    ->withPivot('priority')
                    ->withTimestamps();
    }

    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class, 'question_id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOneSelect($query)
    {
        return $query->where('type', 'one_select');
    }

    public function scopeMultipleSelect($query)
    {
        return $query->where('type', 'multiple_select');
    }

    public function savedByStudents(): MorphMany
    {
        return $this->morphMany(SavedContext::class, 'context');
    }

    public function scopeSearchForMobile($query, $search , $student_id)
    {
        return $query->where('text', 'like', "%{$search}%")
            ->whereHas('savedByStudents', function($query) use ($student_id) {
                $query->where('student_id', $student_id);
            });
    }
}
