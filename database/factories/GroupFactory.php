<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->text(30),
            'status' => fake()->boolean(),
            'completed_lessons' => rand(1, 10),
            'teacher_id' => rand(1, 10),
            'assistant_teacher_id' => rand(1, 10),
            'course_id' => rand(1, 4),
        ];
    }
}
