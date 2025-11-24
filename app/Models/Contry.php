<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Constants\Resources;
use App\Models\System\Info\City;
use App\Constants\MediaCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Contry extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table = 'contries';
    protected $guarded = [
        'id'
    ];


    /**
     * @return \App\Models\Contry
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            GenderEnum::MALE,
            Resources::CONTRY,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::CONTRY_FLAG_COLLECTION)->singleFile();
    }

    public function delete()
    {
        deleteFilesFromMedia($this, MediaCollection::CONTRY_FLAG_COLLECTION);
        return parent::delete();
    }

    //Relationships
    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'contry_id');
    }

    public function eLevels(): HasMany
    {
        return $this->hasMany(ELevel::class, 'contry_id');
    }

    public function cLevels(): HasMany
    {
        return $this->hasMany(CLevel::class, 'contry_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'contry_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'contry_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'contry_id');
    }

    public function subUnits(): HasMany
    {
        return $this->hasMany(SubUnit::class, 'contry_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'contry_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'contry_id')->where('role_id', 5);
    }

    public function getStudentsCount(): int
    {
        return $this->students()->count();
    }
}
