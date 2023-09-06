<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccessForCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i < 100; $i++) {
            $random = rand(0, 4);
            for ($j = 1; $j < $random; $j++) {
                DB::table('access_for_courses')->insert([
                    "student_id" => $i,
                    "course_id" => $j,
                    "pay_time" => fake()->dateTime(),
                    "expire_time" => fake()->dateTime(),
                ]);
            }
        }
    }
}
