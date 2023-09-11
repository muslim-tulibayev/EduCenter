<?php

namespace App\Http\Resources\Permission;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // "token" => $this->token,
            // // "id" => $this->id,
            // "name" => $this->name,
            // "permissions" => [
            // additional windows
            [
                "name" => "statistics",
                "value" => true,
                "default" => true,
            ],

            // access for tables (CRUD)
            [
                "name" => 'roles',
                "value" => $this->roles,
                "default" => false
            ],
            [
                "name" => 'users',
                "value" => $this->users,
                "default" => false
            ],
            [
                "name" => 'teachers',
                "value" => $this->teachers,
                "default" => false
            ],
            [
                "name" => 'courses',
                "value" => $this->courses,
                "default" => false
            ],
            [
                "name" => 'lessons',
                "value" => $this->lessons,
                "default" => false
            ],
            [
                "name" => 'groups',
                "value" => $this->groups,
                "default" => false
            ],
            [
                "name" => 'students',
                "value" => $this->students,
                "default" => false
            ],
            [
                "name" => 'parents',
                "value" => $this->stparents,
                "default" => false
            ],
            [
                "name" => 'sessions',
                "value" => $this->sessions,
                "default" => false
            ],
            [
                "name" => 'branches',
                "value" => $this->branches,
                "default" => false
            ],
            [
                "name" => 'rooms',
                "value" => $this->rooms,
                "default" => false
            ],
            [
                "name" => 'schedules',
                "value" => $this->schedules,
                "default" => false
            ],
            [
                "name" => 'cashiers',
                "value" => $this->cashiers,
                "default" => false
            ],
            [
                "name" => 'access_for_courses',
                "value" => $this->access_for_courses,
                "default" => false
            ],
            // ]
        ];
    }
}
