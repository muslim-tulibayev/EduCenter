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
                    "duration" => $this->session->duration,
                ];
            }),
            "room" => $this->whenLoaded("room", function () {
                return [
                    'id' => $this->room->id,
                    'name' => $this->room->name,
                ];
            }),
            "branch" => $this->when(
                $this->relationLoaded('room') && $this->room->relationLoaded('branch'),
                function () {
                    return [
                        'id' => $this->room->branch->id,
                        'name' => $this->room->branch->name,
                    ];
                }
            )
        ];
    }
}
