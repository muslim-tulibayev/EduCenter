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
            "teacher" => $this->whenLoaded('teacher', function () {
                return [
                    "id" => $this->teacher->id,
                    "firstname" => $this->teacher->firstname,
                    "lastname" => $this->teacher->lastname,
                    // "contact" => $this->teacher->contact,
                    "status" => $this->teacher->status,
                ];
            }),
            "assistant_teacher" => $this->whenLoaded('assistant_teacher', function () {
                return [
                    "id" => $this->assistant_teacher->id,
                    "firstname" => $this->assistant_teacher->firstname,
                    "lastname" => $this->assistant_teacher->lastname,
                    // "contact" => $this->assistant_teacher->contact,
                    "status" => $this->assistant_teacher->status,
                ];
            }),
            "course" => $this->whenLoaded('course', function () {
                return [
                    "id" => $this->course->id,
                    "name" => $this->course->name,
                    // $table->unsignedBigInteger('price');
                ];
            }),
            // "branch_id" => $this->branch_id,
        ];
    }
}
