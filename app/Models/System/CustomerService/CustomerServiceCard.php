<?php

namespace App\Models\System\CustomerService;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerServiceCard extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "customer_cards";
    protected $fillable = [
        "user_id",
        "title",
        "description",
        "type",
        "date",
        "admin_answer",
        "deleted_at",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(CustomerServiceMedia::class, "card_id");
    }
}
