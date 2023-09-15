<?php

namespace App\Http\Resources\AccessForCourse;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccessForCourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // $course = Course::find($this->course_id);

        return [
            "id" => $this->id,
            // "student_id" => $this->student_id,
            "course" => $this->whenLoaded('course', function () {
                return [
                    "id" => $this->course->id,
                    "name" => $this->course->name,
                ];
            }),
            "pay_time" => $this->pay_time,
            "expire_time" => $this->expire_time,
        ];
    }
}
