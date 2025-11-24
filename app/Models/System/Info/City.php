<?php

namespace App\Models\System\Info;

use App\Models\Contry;
use App\Models\Users\Store\Store;
use App\Models\DynamicForm\SavedSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;
    protected $table = 'cities';
    protected $fillable = ["name", "contry_id"];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Contry::class, 'contry_id');
    }
}
