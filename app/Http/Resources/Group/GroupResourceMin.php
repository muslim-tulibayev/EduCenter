<?php

namespace App\Http\Resources\Group;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResourceMin extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "teacher_id" => $this->teacher_id,
            "assistant_teacher_id" => $this->assistant_teacher_id,
        ];
    }
}
