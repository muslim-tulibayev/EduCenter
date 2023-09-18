<?php

namespace App\Http\Resources\Exam;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "course" => $this->whenLoaded('course', function () {
                return [
                    "id" => $this->course->id,
                    "name" => $this->course->name,
                    // $table->unsignedBigInteger('price');
                ];
            }),
        ];
    }
}
