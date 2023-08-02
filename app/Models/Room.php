<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "branch_id",
    ];

    public function name()
    {
        return 'Branch: ' . Branch::find($this->branch_id)->name . ', Room: ' . $this->name;
    }
}
