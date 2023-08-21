<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "location",
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function schedules()
    {
        return $this->hasManyThrough(Schedule::class, Room::class);
    }
}
