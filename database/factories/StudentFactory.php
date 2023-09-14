<?php

namespace Database\Factories;

use App\Models\Role;
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
            'contact' => fake()->phoneNumber(),
            'role_id' => Role::where('name', 'student')->first()->id,
            'status' => fake()->boolean(),
            'created_by' => rand(1, 10),
            'created_at' => fake()->dateTime(),
            "lang" => fake()->randomElement(['en', 'ru', 'uz']),
        ];
    }
}
