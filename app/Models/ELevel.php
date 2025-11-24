<?php

namespace App\Models;

use App\Models\User;
use App\Enums\GenderEnum;
use App\Constants\Resources;
use App\Models\Responsibility;
use App\Enums\PublishStatusEnum;
use Spatie\MediaLibrary\HasMedia;
use App\Constants\MediaCollection;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ELevel extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'e_levels';

    protected $guarded = [
        'id'
    ];

    /**
     * @return \App\Models\ELevel
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            GenderEnum::MALE,
            Resources::E_LEVEL,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::E_LEVEL_COLLECTION)->singleFile();
    }

    public function delete()
    {
        deleteFilesFromMedia($this, MediaCollection::E_LEVEL_COLLECTION);
        return parent::delete();
    }

    // Relationships
    public function cLevels(): HasMany
    {
        return $this->hasMany(CLevel::class, 'e_level_id');
    }

    public function publishedCLevels(): HasMany
    {
        return $this->cLevels()->where('publish_status', PublishStatusEnum::PUBLISHED->value);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'e_level_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'e_level_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'e_level_id');
    }

    public function subUnits(): HasMany
    {
        return $this->hasMany(SubUnit::class, 'e_level_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'e_level_id');
    }

    public function publishedLessons(): HasMany
    {
        return $this->lessons()->published();
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'e_level_id')->where('role_id', 5);
    }

    public function getStudentsCount(): int
    {
        return $this->students()->count();
    }

    public function responsibilities(): HasMany
    {
        return $this->hasMany(Responsibility::class, 'e_level_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'e_level_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('publish_status', PublishStatusEnum::PUBLISHED->value);
    }

    public function childsCounts()
    {
        return $this->cLevels()->count();
    }

    public function childsPublishedCounts()
    {
        return $this->publishedCLevels()->count();
    }
}
