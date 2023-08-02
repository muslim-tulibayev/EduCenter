<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupStudentsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // "id" => $this->id,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            // "email" => $this->email,
            // "contact_no" => $this->contact_no,
            // "status" => $this->status,
            // "created_by" => $this->created_by,
            // "updated_by" => $this->updated_by,
            // "created_at" => $this->created_at,
            // "updated_at" => $this->updated_at,
        ];
    }
}
