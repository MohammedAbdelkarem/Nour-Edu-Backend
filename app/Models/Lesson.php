<?php

namespace App\Models;

use App\Models\File;
use App\Models\Quiz;
use App\Constants\Resources;
use App\Models\Responsibility;
use App\Models\SavedContext;
use App\Enums\PublishStatusEnum;
use Spatie\MediaLibrary\HasMedia;
use App\Constants\MediaCollection;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Lesson extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [
        'id'
    ];

    /**
     * @return \App\Models\Lesson
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            null,
            Resources::LESSON,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::LESSON_COLLECTION);
        $this->addMediaCollection(MediaCollection::LESSON_VIDEO_COLLECTION);
    }

    public function delete()
    {
        // Delete all media files (images and video)
        deleteFilesFromMedia($this, MediaCollection::LESSON_COLLECTION);
        deleteFilesFromMedia($this, MediaCollection::LESSON_VIDEO_COLLECTION);
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

    public function quizzes()
    {
        return $this->morphMany(Quiz::class, 'context');
    }

    public function publishedQuizzes()
    {
        return $this->quizzes()->published();
    }

    public function files()
    {
        return $this->morphMany(File::class, 'context');
    }

    public function publishedFiles()
    {
        return $this->files()->published();
    }

    public function lessonQuestions(): HasMany
    {
        return $this->hasMany(LessonQuestion::class, 'lesson_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'lesson_id');
    }

    public function existComments()
    {
        return $this->comments()->exist();
    }

    public function responsibilities(): HasMany
    {
        return $this->hasMany(Responsibility::class, 'lesson_id');
    }

    public function unlockedContexts(): MorphMany
    {
        return $this->morphMany(UnlockedContext::class, 'context');
    }

    public function lessonRates(): HasMany
    {
        return $this->hasMany(LessonRate::class, 'lesson_id');
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class, 'lesson_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'lesson_id');
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

    public function scopeFilter($query, $data)
    {
        return $query->when(isset($data['search']), function ($query) use ($data) {
            $query->where('name', 'like', '%' . $data['search'] . '%')
                ->orWhere('bio', 'like', '%' . $data['search'] . '%');
        });
    }

    public function viewers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'lesson_student', 'lesson_id', 'student_id')
                    ->withPivot('watched_at')
                    ->withTimestamps();
    }

    public function savedByStudents(): MorphMany
    {
        return $this->morphMany(SavedContext::class, 'context');
    }

    public function filesCounts()
    {
        return $this->files()->count();
    }

    public function publishedFilesCounts()
    {
        return $this->publishedFiles()->count();
    }

    public function quizzesCounts()
    {
        return $this->quizzes()->count();
    }

    public function publishedQuizzesCounts()
    {
        return $this->publishedQuizzes()->count();
    }
}
