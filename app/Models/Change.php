<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Change extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "changeable_id",
        "changeable_type",
        "linkedable_id",
        "linkedable_type",
        "change_description",
        "data_key",
        "created_at"
    ];

    public function changeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function linkedable(): MorphTo
    {
        return $this->morphTo();
    }
}
