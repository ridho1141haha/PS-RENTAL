<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;

class DeviceSeeder extends Seeder {
    public function run() {
        Device::insert([
            ['name' => 'PS3 - Unit A', 'type' => 'PS3', 'price_per_hour' => 5000, 'status' => 'available'],
            ['name' => 'PS4 - Unit A', 'type' => 'PS4', 'price_per_hour' => 8000, 'status' => 'available'],
            ['name' => 'PS5 - Unit A', 'type' => 'PS5', 'price_per_hour' => 12000, 'status' => 'available'],
        ]);
    }
}
