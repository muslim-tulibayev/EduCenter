<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessForCourse extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "student_id",
        "course_id",
        "pay_time",
        "expire_time",
    ];
}
