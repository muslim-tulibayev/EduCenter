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
            [
                "start" => "08:30:00",
                "end" => "09:50:00",
            ],
            [
                "start" => "10:00:00",
                "end" => "11:20:00",
            ],
            [
                "start" => "11:30:00",
                "end" => "12:50:00",
            ],
            [
                "start" => "13:30:00",
                "end" => "14:50:00",
            ],
            [
                "start" => "15:00:00",
                "end" => "16:20:00",
            ],
            [
                "start" => "16:30:00",
                "end" => "17:50:00",
            ]
        ];

        Session::insert($sessions);
    }
}
