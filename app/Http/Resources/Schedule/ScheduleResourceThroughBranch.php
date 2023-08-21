<?php

namespace App\Http\Resources\Schedule;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResourceThroughBranch extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "group" => [
                "id" => $this->group->id,
                "name" => $this->group->name,
            ],
            "weekday" => $this->weekday,
            "session" => $this->session,
            "room" => [
                "id" => $this->room->id,
                "name" => $this->room->name,
            ]
        ];
    }
}
