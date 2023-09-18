<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Mark extends Model
{
    use HasFactory;

    protected $fillable = [
        "value",
        "comment",
        "teacher_id",
        "student_id",
        "markable_type",
        "markable_id",
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function markable(): MorphTo
    {
        return $this->morphTo();
    }
}
