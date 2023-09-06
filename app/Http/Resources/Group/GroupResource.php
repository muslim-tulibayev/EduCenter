<?php

namespace App\Http\Resources\Group;

use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $teacher = Teacher::find($this->teacher_id);
        $assistant_teacher = Teacher::find($this->assistant_teacher_id);

        return [
            "id" => $this->id,
            "name" => $this->name,
            "status" => $this->status,
            "completed_lessons" => $this->completed_lessons,
            "teacher" => $teacher->firstname . ' ' . $teacher->lastname,
            "assistant_teacher" => $assistant_teacher->firstname . ' ' . $assistant_teacher->lastname,
            "course" => Course::find($this->course_id)->name,
        ];
    }
}
