<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            ["name" => 'Tashkent', "location" => 'This is location'],
            ["name" => 'Branch Samarkand', "location" => 'This is location'],
            ["name" => 'Branch Jizzakh', "location" => 'This is location'],
        ];

        Branch::insert($branches);
    }
}
