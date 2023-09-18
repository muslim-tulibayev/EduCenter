<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mark>
 */
class MarkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "value" => rand(1, 100),
            "teacher_id" => rand(1, 10),
            "student_id" => rand(1, 100),
            "markable_id" => rand(1, 100),
            "markable_type" => fake()->randomElement([Lesson::class, Exam::class]),
            "comment" => fake()->text(),
            "created_at" => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
        ];
    }
}
