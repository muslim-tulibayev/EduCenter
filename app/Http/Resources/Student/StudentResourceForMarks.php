<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResourceForMarks extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            // "email" => $this->email,
            // "contact" => $this->contact,
            "status" => $this->status,
            // "created_by" => $this->created_by,
            // "created_at" => $this->created_at,
            "mark" => $this->whenLoaded('marks', function () {
                $mark = $this->marks->first();
                if (!$mark)
                    return null;
                return [
                    "id" => $mark->id,
                    "value" => $mark->value,
                ];
            }),
        ];
    }
}
