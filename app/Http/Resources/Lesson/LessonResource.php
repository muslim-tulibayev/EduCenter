<?php

namespace App\Http\Resources\Lesson;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "sequence_number" => $this->sequence_number,
            "name" => $this->name,
            "course_id" => $this->course_id,
        ];
    }
}
