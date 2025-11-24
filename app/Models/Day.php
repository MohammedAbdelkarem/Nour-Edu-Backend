<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function medicine_days()
    {
        return $this->hasMany(MedicineDay::class);
    }
}
