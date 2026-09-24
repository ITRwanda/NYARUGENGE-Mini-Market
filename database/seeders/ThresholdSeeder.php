<?php

namespace Database\Seeders;

use App\Models\Market;
use App\Models\Threshold;
use Illuminate\Database\Seeder;

class ThresholdSeeder extends Seeder
{
    public function run(): void
    {
        $market = Market::where('name', 'like', '%Nyarugenge%')->firstOrFail();

        // Market-wide thresholds (apply to all devices)
        $thresholds = [
            [
                'parameter'     => 'temperature',
                'minimum_value' => 5.00,
                'maximum_value' => 35.00,
                'unit'          => '°C',
            ],
            [
                'parameter'     => 'humidity',
                'minimum_value' => 20.00,
                'maximum_value' => 80.00,
                'unit'          => '%',
            ],
            [
                'parameter'     => 'gas_level',
                'minimum_value' => null,
                'maximum_value' => 400.00,
                'unit'          => 'ppm',
            ],
        ];

        foreach ($thresholds as $t) {
            Threshold::create([
                'market_id'     => $market->id,
                'device_id'     => null,
                'parameter'     => $t['parameter'],
                'minimum_value' => $t['minimum_value'],
                'maximum_value' => $t['maximum_value'],
                'unit'          => $t['unit'],
                'is_active'     => true,
            ]);
        }
    }
}
