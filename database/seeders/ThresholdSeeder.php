<?php

namespace Database\Seeders;

use App\Models\Threshold;
use Illuminate\Database\Seeder;

class ThresholdSeeder extends Seeder
{
    public function run(): void
    {
        $markets = \App\Models\Market::all();

        foreach ($markets as $market) {
            // Temperature threshold
            Threshold::create([
                'market_id'     => $market->id,
                'device_id'     => null,
                'parameter'     => 'temperature',
                'minimum_value' => 5.00,
                'maximum_value' => 35.00,
                'unit'          => '°C',
                'is_active'     => true,
            ]);

            // Humidity threshold
            Threshold::create([
                'market_id'     => $market->id,
                'device_id'     => null,
                'parameter'     => 'humidity',
                'minimum_value' => 20.00,
                'maximum_value' => 80.00,
                'unit'          => '%',
                'is_active'     => true,
            ]);

            // Gas level threshold
            Threshold::create([
                'market_id'     => $market->id,
                'device_id'     => null,
                'parameter'     => 'gas_level',
                'minimum_value' => null,
                'maximum_value' => 400.00,
                'unit'          => 'ppm',
                'is_active'     => true,
            ]);
        }
    }
}
