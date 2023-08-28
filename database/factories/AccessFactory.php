<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Access>
 */
class AccessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // "student_id" => ,
            // "url" => fake()->imageUrl(),
            "student_id" => rand(1, 100),
            "course_id" => rand(1, 4),
            "pay_time" => fake()->dateTimeThisYear()->format('Y-m-d H:i:s'),
            "expire_time" => fake()->dateTimeThisYear()->format('Y-m-d H:i:s'),
        ];
    }
}
