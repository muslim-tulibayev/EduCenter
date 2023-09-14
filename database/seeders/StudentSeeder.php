<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                "firstname" => fake()->firstName(), // 
                "lastname" => fake()->lastName(), // 
                "email" => "student@gmail.com", // unique()->index('idx_email');
                "password" => Hash::make('12345678'), // 
                "contact" => fake()->phoneNumber(), // 
                "role_id" => Role::where('name', 'student')->first()->id, // constrained();
                "status" => true, // default(false);
                // "payment_token" => "", // nullable();
                "created_by" => 1, // 
                "created_at" => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
                "lang" => 'ru', // ['en', 'ru', 'uz'])->default('en');
            ],
        ];

        Student::insert($students);
        Student::factory(100)->create();
    }
}
