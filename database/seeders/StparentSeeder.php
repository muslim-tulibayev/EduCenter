<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Stparent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StparentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parents = [
            [
                "firstname" => fake()->firstName(), // 
                "lastname" => fake()->lastName(), // 
                "email" => "parent@gmail.com", // ->unique()->index('idx_email');
                "password" => Hash::make('12345678'), // 
                "contact_no" => fake()->phoneNumber(), // 
                "role_id" => Role::where('name', 'parent')->first()->id, // ->constrained();
                // "payment_token" => "", // ->nullable();
            ],
        ];

        Stparent::insert($parents);
    }
}
