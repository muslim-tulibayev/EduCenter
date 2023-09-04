<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
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
            'status' => fake()->boolean(70),
            'role_id' => Role::where('name', 'teacher')->first()->id,
            'is_assistant' => fake()->boolean(),
        ];
    }
}
