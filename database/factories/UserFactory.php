<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'contact' => fake()->phoneNumber(),
            'role_id' => rand(1, 2),
            'status' => fake()->boolean(70),
            'password' => Hash::make('12345678'),
            "lang" => fake()->randomElement(['en', 'ru', 'uz']),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
