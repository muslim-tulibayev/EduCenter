<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseLessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // "id" => $this->id ,
            "sequence_number" => $this->sequence_number ,
            "name" => $this->name,
            // "course_id" => $this->course_id,
            // "created_by" => $this->created_by,
            // "updated_by" => $this->updated_by,
            // "created_at" => $this->created_at, 
            // "updated_at" => $this->updated_at,
        ];
    }
}
