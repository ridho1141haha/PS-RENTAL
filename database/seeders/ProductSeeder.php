<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Indomie Goreng + Telur', 'type' => 'snack', 'price' => 10000, 'stock' => 50],
            ['name' => 'Es Teh Manis', 'type' => 'drink', 'price' => 5000, 'stock' => 100],
            ['name' => 'Kopi Kapal Api', 'type' => 'drink', 'price' => 5000, 'stock' => 30],
            ['name' => 'Chitato Sapi Panggang', 'type' => 'snack', 'price' => 8000, 'stock' => 20],
            ['name' => 'Extra Controller PS4', 'type' => 'other', 'price' => 5000, 'stock' => 5],
            ['name' => 'Extra Controller PS5', 'type' => 'other', 'price' => 10000, 'stock' => 5],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}
