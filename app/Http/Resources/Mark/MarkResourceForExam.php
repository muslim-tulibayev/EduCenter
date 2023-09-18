<?php

namespace App\Http\Resources\Mark;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarkResourceForExam extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "value" => $this->value,
            "exam" => $this->whenLoaded('markable', function () {
                return [
                    "id" => $this->markable->id,
                    "name" => $this->markable->name,
                ];
            }),
            "teacher" => $this->whenLoaded('teacher', function () {
                return [
                    "id" => $this->teacher->id,
                    "firstname" => $this->teacher->firstname,
                    "lastname" => $this->teacher->lastname,
                    // "contact" => $this->teacher->contact,
                    "status" => $this->teacher->status,
                ];
            }),
            "student" => $this->whenLoaded('student', function () {
                return [
                    "id" => $this->student->id,
                    "firstname" => $this->student->firstname,
                    "lastname" => $this->student->lastname,
                    // "contact" => $this->student->contact,
                    "status" => $this->student->status,
                ];
            }),
            "comment" => $this->comment,
            "created_at" => $this->created_at,
        ];
    }
}
