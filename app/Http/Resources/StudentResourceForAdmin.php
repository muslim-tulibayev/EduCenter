<?php

namespace App\Http\Resources;

use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResourceForAdmin extends JsonResource
{
    public function toArray(Request $request): array
    {
        $groups = [];
        foreach ($this->groups as $group) {
            array_push($groups, [
                "id" => $group->id,
                "name" => $group->name,
                "completed_lessons" => $group->completed_lessons,
                "teacher" => Teacher::find($group->teacher_id)->fullname(),
                "assistant_teacher" => Teacher::find($group->assistant_teacher_id)->fullname(),
                "course" => Course::find($group->course_id)->name,
                "created_by" => $group->created_by,
                "updated_by" => $group->updated_by,
                "created_at" => $group->created_at,
                "updated_at" => $group->updated_at,
            ]);
        }

        $parents = [];
        foreach ($this->stparents as $parent) {
            array_push($parents, [
                "id" => $parent->id,
                "firstname" => $parent->firstname,
                "lastname" => $parent->lastname,
                "email" => $parent->email,
                "contact_no" => $parent->contact_no,
                "created_by" => $parent->created_by,
                "updated_by" => $parent->updated_by,
                "created_at" => $parent->created_at,
                "updated_at" => $parent->updated_at,
            ]);
        }

        return [
            "id" => $this->id,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "email" => $this->email,
            // "password" => $this->password,
            "contact_no" => $this->contact_no,
            "status" => $this->status,
            "created_by" => $this->created_by,
            "updated_by" => $this->updated_by,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "groups" => $groups,
            "parents" => $parents,
        ];
    }
}
