<?php

namespace App\Models\System\Info;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivacyPolicy extends Model
{
    use HasFactory;
    protected $table = "privacy_policies";
    protected $fillable = ["lang", "text", "update_by"];

    public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'update_by');
    }
}
