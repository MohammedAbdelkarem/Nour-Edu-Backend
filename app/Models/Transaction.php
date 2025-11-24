<?php

namespace App\Models;

use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
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

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function unlockedContext(): BelongsTo
    {
        return $this->belongsTo(UnlockedContext::class, 'unlocked_context_id');
    }

    public function scopeExistAmount($query)
    {
        return $query->whereNotNull('amount');
    }
    public function scopeFilter($query, $data)
    {
        return $query
        ->when(isset($data['transaction_type']), function($query) use ($data) {
            $query->where('transaction_type', $data['transaction_type']);
        })
        ->when(isset($data['start_amount']), function($query) use ($data) {
            $query->existAmount()->where('amount', '>=', $data['start_amount']);
        })
        ->when(isset($data['end_amount']), function($query) use ($data) {
            $query->existAmount()->where('amount', '<=', $data['end_amount']);
        })
        ->when(isset($data['start_date']), function($query) use ($data) {
            $query->whereDate('created_at', '>=', $data['start_date']);
        })
        ->when(isset($data['end_date']), function($query) use ($data) {
            $query->whereDate('created_at', '<=', $data['end_date']);
        })
        ->when(isset($data['student_ids']) , function($query) use ($data) {
            $query->whereIn('user_id', $data['student_ids']);
        });
    }
}