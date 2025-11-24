<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Constants\Resources;
use App\Constants\MediaCollection;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellPoint extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    
    protected $guarded = [
        'id'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::SELL_POINT_COLLECTION)->singleFile();
    }

    public function delete()
    {
        deleteFilesFromMedia($this , MediaCollection::SELL_POINT_COLLECTION);

        return parent::delete();
    }
    /**
     * @return \App\Models\SellPoint
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            GenderEnum::MALE,
            Resources::SELL_POINT,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }
}
