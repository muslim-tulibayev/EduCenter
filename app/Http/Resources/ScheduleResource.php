<?php

namespace App\Http\Resources;

use App\Models\Group;
use App\Models\Room;
use App\Models\Session;
use App\Models\Weekday;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // "id" => $this->id, 
            "group" => Group::find($this->group_id)->name, 
            "weekday" => Weekday::find($this->weekday_id)->name, 
            "session" => Session::find($this->session_id)->duration, 
            "room" => Room::find($this->room_id)->name(), 
            // "created_by" => $this->created_by, 
            // "updated_by" => $this->updated_by, 
            // "created_at" => $this->created_at, 
            // "updated_at" => $this->updated_at, 
        ];
    }
}
