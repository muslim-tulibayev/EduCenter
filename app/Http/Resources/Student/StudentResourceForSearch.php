<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResourceForSearch extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "fullname" => $this->firstname . ' ' . $this->lastname,
        ];
    }
}
