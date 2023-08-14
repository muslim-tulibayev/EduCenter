<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "student_id",
        "url",
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
