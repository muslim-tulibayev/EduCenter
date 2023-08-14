<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'password' => Hash::make('12345678'),
            'contact_no' => fake()->phoneNumber(),
            'is_paid' => fake()->boolean(),
            'status' => fake()->boolean(),
            'created_by' => rand(1, 10),
            'created_at' => fake()->dateTime(),
        ];
    }
}
