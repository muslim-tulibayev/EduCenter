<?php

namespace Database\Seeders;

use App\Models\Session;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sessions = [
            ["duration" => "08:30 - 09:50"],
            ["duration" => "10:00 - 11:20"],
            ["duration" => "11:30 - 12:50"],
            ["duration" => "13:30 - 14:50"],
            ["duration" => "15:00 - 16:20"],
            ["duration" => "16:30 - 17:50"],
        ];

        Session::insert($sessions);
    }
}
