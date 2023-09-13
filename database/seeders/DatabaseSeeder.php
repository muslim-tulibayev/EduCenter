<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        $this->call([
            RoleSeeder::class,
            WeekdaySeeder::class,
            BranchSeeder::class,
            CourseSeeder::class,
            SessionSeeder::class,
            CashierSeeder::class,
            UserSeeder::class,
            TeacherSeeder::class,
            StparentSeeder::class,
            StudentSeeder::class,
            LessonSeeder::class,
            GroupSeeder::class,
            RoomSeeder::class,
            ScheduleSeeder::class,
            CardSeeder::class,
            PaymentSeeder::class,
        ]);

        // Certificate::factory(100)->create();
        // Failedsts::factory(50)->create();
        // Failedgroups::factory(3)->create();

        for ($i = 0; $i < 10; $i++) {
            DB::table('branch_course')->insert([
                'branch_id' => rand(1, 3),
                'course_id' => rand(1, 4),
            ]);
        }

        for ($i = 0; $i < 100; $i++) {
            DB::table('group_student')->insert([
                'group_id' => rand(1, 5),
                'student_id' => rand(1, 100),
            ]);
        }

        for ($i = 0; $i < 100; $i++) {
            DB::table('stparent_student')->insert([
                'stparent_id' => rand(1, 100),
                'student_id' => rand(1, 100),
            ]);
        }

        for ($i = 0; $i < 100; $i++) {
            DB::table('branch_teacher')->insert([
                'branch_id' => rand(1, 3),
                'teacher_id' => rand(1, 10),
            ]);
        }

        // AccessForCourseSeeder::class,
        for ($i = 1; $i <= 100; $i++) {
            $random = rand(0, 4);
            for ($j = 1; $j <= $random; $j++) {
                DB::table('access_for_courses')->insert([
                    "student_id" => $i,
                    "course_id" => $j,
                    "pay_time" => fake()->dateTime(),
                    "expire_time" => fake()->dateTime(),
                ]);
            }
        }

        for ($i = 0; $i < 20; $i++) {
            DB::table('branch_user')->insert([
                'branch_id' => rand(1, 3),
                'user_id' => rand(1, 10),
            ]);
        }

        for ($branch = 1; $branch <= 3; $branch++) {
            for ($session = 1; $session <= 6; $session++) {
                DB::table('branch_session')->insert([
                    'branch_id' => $branch,
                    'session_id' => $session,
                ]);
            }
        }
    }
}
