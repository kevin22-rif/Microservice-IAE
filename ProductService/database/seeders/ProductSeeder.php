<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB Facade

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            [
                'name' => 'Laptop Gaming Pro',
                'description' => 'Laptop canggih untuk gaming dan pekerjaan berat.',
                'price' => 1500.00,
                'stock' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Smartphone Ultra',
                'description' => 'Smartphone dengan kamera terbaik dan performa super.',
                'price' => 800.00,
                'stock' => 120,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Smart TV 4K 55 Inch',
                'description' => 'Televisi pintar dengan resolusi 4K untuk hiburan maksimal.',
                'price' => 750.00,
                'stock' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
