<?php

namespace App\Models\System\CustomerService;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerServiceMedia extends Model
{
    use HasFactory;
    protected $table = "customer_card_media";
    protected $fillable = [
        'card_id',
        'media_url',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(CustomerServiceCard::class, "card_id");
    }
}
