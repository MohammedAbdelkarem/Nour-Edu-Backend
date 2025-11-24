<?php

namespace App\Models;

use App\Constants\Resources;
use App\Models\Responsibility;
use App\Enums\PublishStatusEnum;
use Spatie\MediaLibrary\HasMedia;
use App\Constants\MediaCollection;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'subjects';

    protected $guarded = [
        'id'
    ];

    /**
     * @return \App\Models\Subject
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            null,
            Resources::SUBJECT,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::SUBJECT_COLLECTION)->singleFile();
        $this->addMediaCollection(MediaCollection::SUBJECT_VIDEO_COLLECTION)->singleFile();
        $this->addMediaCollection(MediaCollection::SUBJECT_ICON_COLLECTION)->singleFile();
    }

    public function delete()
    {
        deleteFilesFromMedia($this, MediaCollection::SUBJECT_COLLECTION);
        return parent::delete();
    }

    // Relationships
    public function contry(): BelongsTo
    {
        return $this->belongsTo(Contry::class, 'contry_id');
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

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'subject_id');
    }
    

    public function publishedUnits(): HasMany
    {
        return $this->units()->where('publish_status', PublishStatusEnum::PUBLISHED->value);
    }

    public function subUnits(): HasMany
    {
        return $this->hasMany(SubUnit::class, 'subject_id');
    }

    public function publishedSubUnits(): HasMany
    {
        return $this->subUnits()->published();
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'subject_id');
    }

    public function publishedLessons(): HasMany
    {
        return $this->lessons()->published();
    }

    public function files()
    {
        return $this->morphMany(File::class, 'context');
    }

    public function publishedFiles()
    {
        return $this->files()->published();
    }

    public function quizzes()
    {
        return $this->morphMany(Quiz::class, 'context');
    }

    public function publishedQuizzes()
    {
        return $this->quizzes()->published();
    }

    public function responsibilities(): HasMany
    {
        return $this->hasMany(Responsibility::class, 'subject_id');
    }

    public function unlockedContexts(): MorphMany
    {
        return $this->morphMany(UnlockedContext::class, 'context');
    }

    public function coupons(): MorphMany
    {
        return $this->morphMany(Coupon::class, 'context');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'subject_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('publish_status', PublishStatusEnum::PUBLISHED->value);
    }

    public function numberOfLessons()
    {
        return $this->lessons()->count();
    }
    public function numberOfPublishedLessons()
    {
        return $this->lessons()->published()->count();
    }

    public function scopeFilter($query, $data)
    {
        return $query->when(isset($data['search']), function ($query) use ($data) {
            $query->where('name', 'like', '%' . $data['search'] . '%')
                ->orWhere('bio', 'like', '%' . $data['search'] . '%')
                ->orWhereHas('course', function ($query) use ($data) {
                    $query->where('name', 'like', '%' . $data['search'] . '%')
                        ->orWhere('bio', 'like', '%' . $data['search'] . '%');
                });
        });
    }

    public function childsCounts()
    {
        return $this->units()->count();
    }

    public function childsPublishedCounts()
    {
        return $this->publishedUnits()->count();
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
