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
            "teacher_id" => $this->teacher_id,
            "assistant_teacher_id" => $this->assistant_teacher_id,
            "course_id" => $this->course_id,
            // "branch_id" => $this->branch_id,
        ];
    }
}
