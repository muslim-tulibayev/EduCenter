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
                "contact_no" => fake()->phoneNumber(), // 
                "email" => "superadmin@gmail.com", // unique()->index('idx_email');
                "password" => Hash::make('12345678'), // 
                "status" => true, // default(false);
                "role_id" => Role::where('name', 'superadmin')->first()->id, // constrained();
                // "branch_id" => "1", // constrained();
            ],
            [
                "firstname" => fake()->firstName(), // 
                "lastname" => fake()->lastName(), // 
                "contact_no" => fake()->phoneNumber(), // 
                "email" => "admin@gmail.com", // unique()->index('idx_email');
                "password" => Hash::make('12345678'), // 
                "status" => true, // default(false);
                "role_id" => Role::where('name', 'admin')->first()->id, // constrained();
                // "branch_id" => "2", // constrained();
            ],
        ];

        User::insert($users);
        User::factory(10)->create();
    }
}
