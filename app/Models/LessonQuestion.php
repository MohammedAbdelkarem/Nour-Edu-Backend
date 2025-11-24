<?php

namespace App\Models;

use App\Constants\Resources;
use App\Constants\MediaCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class LessonQuestion extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $guarded = [
        'id'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::LESSON_QUESTION_COLLECTION);
        $this->addMediaCollection(MediaCollection::LESSON_QUESTION_ANSWER_COLLECTION);
    }

    public function delete()
    {
        deleteFilesFromMedia($this, MediaCollection::LESSON_QUESTION_COLLECTION);
        deleteFilesFromMedia($this, MediaCollection::LESSON_QUESTION_ANSWER_COLLECTION);
        return parent::delete();
    }

    /**
     * @return \App\Models\LessonQuestion
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            null,
            Resources::RES_LESSON_QUESTION,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }

    // Relationships
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    // Scopes
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByLesson($query, $lessonId)
    {
        return $query->where('lesson_id', $lessonId);
    }

    public function scopeAnswered($query)
    {
        return $query->whereNotNull('answer');
    }

    public function scopeUnanswered($query)
    {
        return $query->whereNull('answer');
    }
}
