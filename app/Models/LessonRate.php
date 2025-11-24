<?php

namespace App\Models;

use App\Constants\Resources;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonRate extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    /**
     * @return \App\Models\LessonRate
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            null,
            Resources::LESSON_RATE,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
