<?php

namespace Database\Seeders;

use App\Models\Cashier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CashierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'cashier_id' => '5e730e8e0b852a417aa49ceb',
                'cashier_key' => 'ZPDODSiTYKuX0jyO7Kl2to4rQbNwG08jbghj'
            ],
        ];

        Cashier::insert($roles);
    }
}
