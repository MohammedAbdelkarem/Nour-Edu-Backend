<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Constants\Resources;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavedContext extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    /**
     * @return \App\Models\SavedContext
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            GenderEnum::MALE,
            Resources::SAVED_CONTEXT,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function context(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeByStudent($query, $student_id)
    {
        return $query->where('student_id', $student_id);
    }

    public function scopeLessons($query)
    {
        return $query->where('context_type', Lesson::class);
    }

    public function scopeQuestions($query)
    {
        return $query->where('context_type', Question::class);
    }
}