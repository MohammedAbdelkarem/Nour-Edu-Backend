<?php

namespace App\Models\System\Info;

use App\Models\DynamicForm\SavedSearch;
use App\Models\Users\Store\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;
    protected $fillable = ["name"];

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }

    public function searches(): HasMany
    {
        return $this->hasMany(SavedSearch::class, 'city_id');
    }
}
