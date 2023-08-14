<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "branch_id",
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // public function name()
    // {
    //     return 'Branch: ' . Branch::find($this->branch_id)->name . ', Room: ' . $this->name;
    // }
}
