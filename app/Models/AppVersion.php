<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Constants\Resources;
use Spatie\MediaLibrary\HasMedia;
use App\Constants\MediaCollection;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppVersion extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;
    
    protected $guarded = [
        'id'
    ];


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::APP_VERSION_COLLECTION)->singleFile();
    }

    public function delete()
    {
        deleteFilesFromMedia($this , MediaCollection::APP_VERSION_COLLECTION);
        return parent::delete();
    }

    /**
     * @return \App\Models\AppVersion
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            GenderEnum::MALE,
            Resources::RES_APP_VERSION,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }
}
