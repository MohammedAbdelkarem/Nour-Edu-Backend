<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Constants\Resources;
use App\Enums\CouponTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'number_of_max_uses' => 'integer',
        'number_of_uses' => 'integer',
        'is_expired' => 'boolean',
    ];
    

    /**
     * @return \App\Models\Coupon
     */
    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            GenderEnum::MALE,
            Resources::COUPON,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }

    // Relationships
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'coupon_id');
    }

    public function context(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeFilter($query, $data)
    {
        return $query
            ->when(isset($data['type']), function($query) use ($data) {
                $query->where('type', $data['type']);
            })
            ->when(isset($data['is_expired']) && $data['is_expired'] == 1, function($query) {
                $query->where('is_expired', 1);
            })
            ->when(isset($data['is_expired']) && $data['is_expired'] == 0, function($query) {
                $query->where('is_expired', 0);
            })
            ->when(isset($data['created_at']), function($query) use ($data) {
                $query->whereDate('created_at', $data['created_at']);
            });
    }
}