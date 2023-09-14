<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = [
            [
                "firstname" => fake()->firstName(), // 
                "lastname" => fake()->lastName(), // 
                "email" => "teacher@gmail.com", // unique()->index('idx_email');
                "password" => Hash::make('12345678'), // 
                "contact" => fake()->phoneNumber(), // 
                "is_assistant" => false, // 
                "status" => true, // 
                "role_id" => Role::where('name', 'teacher')->first()->id, // constrained();
                "lang" => 'ru', // ['en', 'ru', 'uz'])->default('en');
            ],
        ];

        Teacher::insert($teachers);
        Teacher::factory(10)->create();
    }
}
