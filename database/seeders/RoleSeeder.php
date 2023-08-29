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
                'name' => 'admin',
                'roles' => 4,
                'users' => 4,
                // 'weekdays' => 0,
                'teachers' => 4,
                'courses' => 4,
                'lessons' => 4,
                'groups' => 4,
                'students' => 4,
                'stparents' => 4,
                'sessions' => 4,
                'branches' => 4,
                'rooms' => 4,
                'schedules' => 4,
                // 'changes' => 0,
                // 'certificates' => 0,
                // 'failedsts' => 0,
                // 'failedgroups' => 0,
                'cashiers' => 4,
                'access_for_courses' => 4,

                'student_search' => 1,
                'payment_addcard' => 1,
                'payment_cashier' => 1,
                'payment_pay' => 1,
            ],
            [
                'name' => 'teacher',
                'roles' => 0,
                'users' => 1,
                // 'weekdays' => 0,
                'teachers' => 2,
                'courses' => 1,
                'lessons' => 1,
                'groups' => 1,
                'students' => 1,
                'stparents' => 1,
                'sessions' => 1,
                'branches' => 1,
                'rooms' => 1,
                'schedules' => 1,
                // 'changes' => 0,
                // 'certificates' => 0,
                // 'failedsts' => 0,
                // 'failedgroups' => 0,
                'cashiers' => 1,
                'access_for_courses' => 1,

                'student_search' => 1,
                'payment_addcard' => 1,
                'payment_cashier' => 1,
                'payment_pay' => 1,
            ],[
                'name' => 'parent',
                'roles' => 0,
                'users' => 1,
                // 'weekdays' => 0,
                'teachers' => 1,
                'courses' => 1,
                'lessons' => 1,
                'groups' => 1,
                'students' => 1,
                'stparents' => 2,
                'sessions' => 1,
                'branches' => 1,
                'rooms' => 1,
                'schedules' => 1,
                // 'changes' => 0,
                // 'certificates' => 0,
                // 'failedsts' => 0,
                // 'failedgroups' => 0,
                'cashiers' => 1,
                'access_for_courses' => 1,

                'student_search' => 1,
                'payment_addcard' => 1,
                'payment_cashier' => 1,
                'payment_pay' => 1,
            ],
            [
                'name' => 'student',
                'roles' => 0,
                'users' => 1,
                // 'weekdays' => 0,
                'teachers' => 1,
                'courses' => 1,
                'lessons' => 1,
                'groups' => 1,
                'students' => 2,
                'stparents' => 1,
                'sessions' => 1,
                'branches' => 1,
                'rooms' => 1,
                'schedules' => 1,
                // 'changes' => 0,
                // 'certificates' => 0,
                // 'failedsts' => 0,
                // 'failedgroups' => 0,
                'cashiers' => 1,
                'access_for_courses' => 1,

                'student_search' => 1,
                'payment_addcard' => 1,
                'payment_cashier' => 1,
                'payment_pay' => 1,
            ],
        ];

        Role::insert($roles);
    }
}
