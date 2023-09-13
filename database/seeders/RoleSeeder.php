<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'superadmin',

                'roles' => 4,
                'users' => 4,
                'inactive_users' => 4,
                // 'weekdays' => 0,
                'teachers' => 4,
                'assistant_teachers' => 4,
                'courses' => 4,
                'lessons' => 4,
                'groups' => 4,
                'students' => 4,
                'stparents' => 4,
                'sessions' => 4,
                'branches' => 4,
                'rooms' => 4,
                'schedules' => 4,
                // 'certificates' => 0,
                // 'failedsts' => 0,
                // 'failedgroups' => 0,
                'cashiers' => 4,
                'access_for_courses' => 4,
                'cards' => 4,
                'payments' => 4,
                // 'changes' => 0,
            ],
            [
                'name' => 'admin',

                'roles' => 0,
                'users' => 1,
                'inactive_users' => 1,
                // 'weekdays' => 0,
                'teachers' => 4,
                'assistant_teachers' => 4,
                'courses' => 1,
                'lessons' => 1,
                'groups' => 4,
                'students' => 4,
                'stparents' => 4,
                'sessions' => 4,
                'branches' => 1,
                'rooms' => 4,
                'schedules' => 4,
                // 'certificates' => 0,
                // 'failedsts' => 0,
                // 'failedgroups' => 0,
                'cashiers' => 4,
                'access_for_courses' => 4,
                'cards' => 1,
                'payments' => 1,
                // 'changes' => 0,
            ],
            [
                'name' => 'teacher',

                'roles' => 0,
                'users' => 0,
                'inactive_users' => 0,
                // 'weekdays' => 0,
                'teachers' => 0,
                'assistant_teachers' => 0,
                'courses' => 0,
                'lessons' => 0,
                'groups' => 0,
                'students' => 0,
                'stparents' => 0,
                'sessions' => 0,
                'branches' => 0,
                'rooms' => 0,
                'schedules' => 0,
                // 'certificates' => 0,
                // 'failedsts' => 0,
                // 'failedgroups' => 0,
                'cashiers' => 0,
                'access_for_courses' => 0,
                'cards' => 0,
                'payments' => 0,
                // 'changes' => 0,
            ],
            [
                'name' => 'parent',

                'roles' => 0,
                'users' => 0,
                'inactive_users' => 0,
                // 'weekdays' => 0,
                'teachers' => 0,
                'assistant_teachers' => 0,
                'courses' => 0,
                'lessons' => 0,
                'groups' => 0,
                'students' => 0,
                'stparents' => 0,
                'sessions' => 0,
                'branches' => 0,
                'rooms' => 0,
                'schedules' => 0,
                // 'certificates' => 0,
                // 'failedsts' => 0,
                // 'failedgroups' => 0,
                'cashiers' => 0,
                'access_for_courses' => 0,
                'cards' => 0,
                'payments' => 0,
                // 'changes' => 0,
            ],
            [
                'name' => 'student',

                'roles' => 0,
                'users' => 0,
                'inactive_users' => 0,
                // 'weekdays' => 0,
                'teachers' => 0,
                'assistant_teachers' => 0,
                'courses' => 0,
                'lessons' => 0,
                'groups' => 0,
                'students' => 0,
                'stparents' => 0,
                'sessions' => 0,
                'branches' => 0,
                'rooms' => 0,
                'schedules' => 0,
                // 'certificates' => 0,
                // 'failedsts' => 0,
                // 'failedgroups' => 0,
                'cashiers' => 0,
                'access_for_courses' => 0,
                'cards' => 0,
                'payments' => 0,
                // 'changes' => 0,
            ],
        ];

        Role::insert($roles);
    }
}
