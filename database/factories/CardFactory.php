<?php

namespace Database\Factories;

use App\Models\Stparent;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cardable_id' => rand(1, 50),
            'cardable_type' => fake()->randomElement([Stparent::class, Student::class]),
            'card_number' => fake()->creditCardNumber(),
            'card_expiration' => rand(1, 12) . '/' . rand(23, 30),
            'card_token' => fake()->text(50),
        ];
    }
}
