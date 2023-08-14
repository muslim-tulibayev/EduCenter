<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "group_id",
        "weekday_id",
        "session_id",
        "room_id",
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function weekday(): BelongsTo
    {
        return $this->belongsTo(Weekday::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    // public function branch(): BelongsTo
    // {
    //     return $this->room->belongsTo(Branch::class);
    // }
}
