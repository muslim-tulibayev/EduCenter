<?php

namespace App\Http\Resources;

use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // "id" => $this->id,
            "name" => $this->name,
            "completed_lessons" => $this->completed_lessons,
            "teacher" => Teacher::find($this->teacher_id)->fullname(),
            "assistant_teacher" => Teacher::find($this->assistant_teacher_id)->fullname(),
            "course" => Course::find($this->course_id)->name,
            // "created_by" => $this->created_by,
            // "updated_by" => $this->updated_by,
            // "created_at" => $this->created_at,
            // "updated_at" => $this->updated_at,
        ];
    }
}
