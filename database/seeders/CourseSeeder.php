<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            ["name" => "Backend: Laravel", "price" => 500000],
            ["name" => "Backend: NodeJs", "price" => 600000],
            ["name" => "Frontend: VueJs", "price" => 700000],
            ["name" => "Frontend: Angular", "price" => 800000],
        ];

        Course::insert($courses);
    }
}
