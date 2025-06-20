<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB Facade

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('orders')->insert([
            [
                'user_id' => 1, // Asumsi ada user_id 1 dari UserService
                'total_amount' => 1500.00,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2, // Asumsi ada user_id 2 dari UserService
                'total_amount' => 800.00,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'total_amount' => 750.00,
                'status' => 'shipped',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
