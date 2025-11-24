<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ELevel;
use App\Models\CLevel;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\SubUnit;
use App\Models\Lesson;
use App\Enums\PublishStatusEnum;

class Responsibility extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    // Relationships
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

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

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('publish_status', PublishStatusEnum::PUBLISHED->value);
    }

    public function publishedELevel(): BelongsTo
    {
        return $this->eLevel()->published();
    }

    public function publishedCLevel(): BelongsTo
    {
        return $this->cLevel()->published();
    }

    public function publishedCourse(): BelongsTo
    {
        return $this->course()->published();
    }

    public function publishedSubject(): BelongsTo
    {
        return $this->subject()->published();
    }

    public function publishedUnit(): BelongsTo
    {
        return $this->unit()->published();
    }

    public function publishedSubUnit(): BelongsTo
    {
        return $this->subUnit()->published();
    }

    public function publishedLesson(): BelongsTo
    {
        return $this->lesson()->published();
    }
}