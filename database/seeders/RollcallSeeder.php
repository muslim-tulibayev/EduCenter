<?php

namespace Database\Seeders;

use App\Models\Rollcall;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RollcallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rollcall::factory(100)->create();
    }
}
