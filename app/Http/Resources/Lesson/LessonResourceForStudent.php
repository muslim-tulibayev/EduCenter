<?php

namespace App\Http\Resources\Lesson;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResourceForStudent extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "sequence_number" => $this->sequence_number,
            "name" => $this->name,
            "mark" => $this->whenLoaded('markable', function () {
                $mark = $this->markable->first();
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
