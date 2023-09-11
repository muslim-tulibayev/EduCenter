<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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

            // access for tables (CRUD)
            "roles" => $this->roles,
            "users" => $this->users,
            // "weekdays" => $this->weekdays,
            "teachers" => $this->teachers,
            "courses" => $this->courses,
            "lessons" => $this->lessons,
            "groups" => $this->groups,
            "students" => $this->students,
            "stparents" => $this->stparents,
            "sessions" => $this->sessions,
            "branches" => $this->branches,
            "rooms" => $this->rooms,
            "schedules" => $this->schedules,
            // "changes" => $this->changes,
            // "certificates" => $this->certificates,
            // "failedsts" => $this->failedsts,
            // "failedgroups" => $this->failedgroups,
            "cashiers" => $this->cashiers,
            "access_for_courses" => $this->access_for_courses,

            // access for functionalities
            // "student_search" => $this->student_search,
            // "payment_addcard" => $this->payment_addcard,
            // "payment_cashier" => $this->payment_cashier,
            // "payment_pay" => $this->payment_pay,
        ];
    }
}
