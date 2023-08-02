<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Lesson;
use App\Models\Room;
use App\Models\Schedule;
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
        ]);


        User::factory(10)->create();
        Teacher::factory(10)->create();
        Lesson::factory(100)->create();
        Group::factory(5)->create();
        Student::factory(100)->create();
        for ($i = 0; $i < 100; $i++) {
            DB::table('group_student')->insert([
                'group_id' => rand(1, 5),
                'student_id' => rand(1, 100),
            ]);
        }
        Stparent::factory(100)->create();
        for ($i = 0; $i < 100; $i++) {
            DB::table('stparent_student')->insert([
                'stparent_id' => rand(1, 100),
                'student_id' => rand(1, 100),
            ]);
        }
        Room::factory(100)->create();
        Schedule::factory(100)->create();
    }
}
