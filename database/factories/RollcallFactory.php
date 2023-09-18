<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rollcall>
 */
class RollcallFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "student_id" => rand(1, 100),
            "lesson_id" => rand(1, 100),
            "created_at" => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
        ];
}
}
