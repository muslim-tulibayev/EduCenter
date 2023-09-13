<?php

namespace Database\Factories;

use App\Models\Stparent;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
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
            "type" => fake()->randomElement(['card', 'cash']),
            "amount" => fake()->randomElement([1000000, 500000, 250000, 1500000]),
            "paymentable_id" => rand(1, 12),
            "paymentable_type" => fake()->randomElement([Stparent::class, Student::class, User::class]),
            "created_at" => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
        ];
    }
}
