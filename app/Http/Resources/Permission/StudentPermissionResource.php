<?php

namespace App\Http\Resources\Permission;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentPermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // additional windows
            [
                "name" => "statistics",
                "value" => false,
                "default" => false,
            ],
            [
                "name" => "my-groups",
                "value" => false,
                "default" => false,
            ],
            [
                "name" => "my-children",
                "value" => false,
                "default" => false,
            ],
            [
                "name" => "my-courses",
                "value" => true,
                "default" => true,
            ],
            [
                "name" => "my-cards",
                "value" => true,
                "default" => false,
            ],
            [
                "name" => "all-courses",
                "value" => true,
                "default" => false,
            ],


            // access for tables (CRUD)
            // "id" => $this->id,
            // "name" => $this->name,
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
                "name" => 'access-for-courses',
                "value" => $this->access_for_courses,
                "default" => false
            ],
        ];
    }
}
