<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Constants\Resources;
use App\Enums\CommentStatusEnum;
use App\Models\Scopes\LoadUserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new LoadUserScope);
    }

    /**
     * @return \App\Models\Comment
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            GenderEnum::FEMALE,
            Resources::RES_COMMENT,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function replay(): HasOne
    {
        return $this->hasOne(Replay::class, 'comment_id');
    }

    public function existReplay()
    {
        return $this->replay()->exist();
    }

    // Scopes
    public function scopeExist($query)
    {
        return $query->where('status', CommentStatusEnum::EXIST->value);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }
}
