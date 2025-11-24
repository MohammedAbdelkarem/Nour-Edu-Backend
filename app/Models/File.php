<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Constants\Resources;
use App\Enums\PublishStatusEnum;
use Spatie\MediaLibrary\HasMedia;
use App\Constants\MediaCollection;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $guarded = [
        'id'
    ];

    /**
     * @return \App\Models\File
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            GenderEnum::MALE,
            Resources::FILE,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::FILE_COLLECTION)->singleFile();
    }

    public function delete()
    {
        deleteFilesFromMedia($this , MediaCollection::FILE_COLLECTION);
        
        return parent::delete();
    }
    // Relationships
    public function context(): MorphTo
    {
        return $this->morphTo();
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

    public function scopeSearchForMobile($query, $search , $studnet_id = null)
    {
        return $query->published()
            ->where(function($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->whereHas('context', function($query) use ($studnet_id) {
                $query->whereHas('unlockedContexts', function($query) use ($studnet_id) {
                    $query->where('user_id', $studnet_id);
                });
            });
    }

    public function scopeFilterForMobile($query, $data, $student_id)
    {
        return $query->published()
            ->when(isset($data['subject_ids']) || isset($data['unit_ids']) || isset($data['sub_unit_ids']) || isset($data['lesson_ids']), function($query) use ($data) {
                $query->where(function($subQuery) use ($data) {
                    $subQuery->when(isset($data['subject_ids']), function($query) use ($data) {
                        $query->whereIn('context_id', $data['subject_ids'])->where('context_type', Subject::class);
                    })
                    ->when(isset($data['unit_ids']), function($query) use ($data) {
                        $query->orWhereIn('context_id', $data['unit_ids'])->where('context_type', Unit::class);
                    })
                    ->when(isset($data['sub_unit_ids']), function($query) use ($data) {
                        $query->orWhereIn('context_id', $data['sub_unit_ids'])->where('context_type', SubUnit::class);
                    })
                    ->when(isset($data['lesson_ids']), function($query) use ($data) {
                        $query->orWhereIn('context_id', $data['lesson_ids'])->where('context_type', Lesson::class);
                    });
                });
            })
            ->whereHas('context', function($query) use ($student_id) {
                $query->whereHas('unlockedContexts', function($query) use ($student_id) {
                    $query->where('user_id', $student_id);
                });
            });
    }
}
