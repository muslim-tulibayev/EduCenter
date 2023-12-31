<?php

namespace App\Http\Resources\Schedule;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "group" => $this->whenLoaded('group', function () {
                return [
                    "id" => $this->group->id,
                    "name" => $this->group->name,
                ];
            }),
            "weekday" => $this->whenLoaded('weekday', function () {
                return [
                    "id" => $this->weekday->id,
                    "name" => $this->weekday->name,
                ];
            }),
            "session" => $this->whenLoaded('session', function () {
                return [
                    "id" => $this->session->id,
                    "start" => $this->session->start,
                    "end" => $this->session->end,
                ];
            }),
            "room" => $this->whenLoaded("room", function () {
                return [
                    'id' => $this->room->id,
                    'name' => $this->room->name,
                ];
            }),
        ];
    }
}

        // "id" => $this->id,
        // "group_id" => $this->group_id,
        // "weekday_id" => $this->weekday_id,
        // "session_id" => $this->session_id,
        // "room_id" => $this->room_id,