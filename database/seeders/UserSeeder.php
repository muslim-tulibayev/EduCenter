<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                "firstname" => fake()->firstName(), // 
                "lastname" => fake()->lastName(), // 
                "contact" => fake()->phoneNumber(), // 
                "email" => "superadmin@gmail.com", // unique()->index('idx_email');
                "password" => Hash::make('12345678'), // 
                "status" => true, // default(false);
                "role_id" => Role::where('name', 'superadmin')->first()->id, // constrained();
                "lang" => 'uz', // ['en', 'ru', 'uz'])->default('en');
            ],
            [
                "firstname" => fake()->firstName(), // 
                "lastname" => fake()->lastName(), // 
                "contact" => fake()->phoneNumber(), // 
                "email" => "admin@gmail.com", // unique()->index('idx_email');
                "password" => Hash::make('12345678'), // 
                "status" => true, // default(false);
                "role_id" => Role::where('name', 'admin')->first()->id, // constrained();
                "lang" => 'ru', // ['en', 'ru', 'uz'])->default('en');
            ],
        ];

        User::insert($users);
        User::factory(10)->create();
    }
}
