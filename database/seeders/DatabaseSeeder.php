<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MarketSeeder::class,
            VendorSeeder::class,
            StallSeeder::class,
            DeviceSeeder::class,
            ThresholdSeeder::class,
            SensorReadingSeeder::class,
            AlertSeeder::class,
            InspectionSeeder::class,
        ]);
    }
}
