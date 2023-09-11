<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        "start",
        "end",
    ];

    public $timestamps = false;

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class);
    }
}
