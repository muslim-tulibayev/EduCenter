<?php

namespace Database\Seeders;

use App\Models\Failedgroups;
use App\Models\Failedsts;
use App\Models\Group;
use App\Models\Lesson;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Certificate;
use App\Models\Stparent;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        $this->call([
            RoleSeeder::class,
            CourseSeeder::class,
            WeekdaySeeder::class,
            SessionSeeder::class,
            BranchSeeder::class,
            CashierSeeder::class,

            UserSeeder::class,
            TeacherSeeder::class,
            StparentSeeder::class,
            StudentSeeder::class,
        ]);

        User::factory(10)->create();
        Teacher::factory(10)->create();
        Stparent::factory(100)->create();
        Student::factory(100)->create();

        Lesson::factory(100)->create();
        Group::factory(5)->create();
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
        Room::factory(100)->create();
        Schedule::factory(100)->create();
        for ($i = 0; $i < 100; $i++) {
            DB::table('branch_teacher')->insert([
                'branch_id' => rand(1, 3),
                'teacher_id' => rand(1, 10),
            ]);
        }
        for ($i = 0; $i < 100; $i++) {
            DB::table('branch_student')->insert([
                'branch_id' => rand(1, 3),
                'student_id' => rand(1, 100),
            ]);
        }
        Certificate::factory(100)->create();
        Failedsts::factory(50)->create();
        Failedgroups::factory(3)->create();
        // AccessForCourse::factory(50)->create();
    }
}
