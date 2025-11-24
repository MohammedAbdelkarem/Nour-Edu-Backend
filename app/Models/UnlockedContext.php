<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnlockedContext extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function context(): MorphTo
    {
        return $this->morphTo();
    }

    public function transactions(): HasOne
    {
        return $this->hasOne(Transaction::class, 'unlocked_context_id');
    }
}