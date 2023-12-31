<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Parent>
 */
class StparentFactory extends Factory
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
            'contact' => fake()->phoneNumber(),
            'status' => fake()->boolean(50),
            'role_id' => Role::where('name', 'parent')->first()->id,
            'password' => Hash::make('12345678'),
            "lang" => fake()->randomElement(['en', 'ru', 'uz']),
        ];
    }
}
