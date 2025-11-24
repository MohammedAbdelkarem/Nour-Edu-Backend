<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Constants\Resources;
use App\Enums\MediaStatusEnum;
use Spatie\MediaLibrary\HasMedia;
use App\Constants\MediaCollection;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banner extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia , HasTranslations;

    protected $guarded = [
        'id'
    ];

    public $translatable = [
        'title',
        'description'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::BANNER_COLLECTION)->singleFile();
    }
    
    public function bannerable()
    {
        return $this->morphTo();
    }

    public function delete()
    {
        deleteFilesFromMedia($this , MediaCollection::BANNER_COLLECTION);

        return parent::delete();
    }
    /**
     * @return \App\Models\Story
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            Resources::RES_BANNER,
            GenderEnum::FEMALE,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }
    
    public function scopeActive($query)
    {
        return $query
            ->where('status' , MediaStatusEnum::ACTIVE);
    }

    public function scopeCLevel($query)
    {
        return $query->where('bannerable_type', CLevel::class);
    }
}
