<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\SensorReading;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SensorReadingSeeder extends Seeder
{
    public function run(): void
    {
        $devices = Device::where('is_active', true)->get();

        // Generate 7 days of readings every 30 minutes per device
        foreach ($devices as $device) {
            $start = Carbon::now()->subDays(7);
            $end   = Carbon::now();

            $current = $start->copy();

            while ($current->lte($end)) {
                // Simulate realistic sensor patterns
                $hour = $current->hour;

                // Temperature rises mid-day
                $baseTemp = 22 + ($hour >= 10 && $hour <= 16 ? rand(0, 10) : rand(-2, 3));
                $temp     = round($baseTemp + (rand(-20, 20) / 10), 2);

                // Humidity inversely correlates with temp somewhat
                $baseHumidity = 55 - ($temp - 22) * 0.5;
                $humidity     = round(min(95, max(10, $baseHumidity + (rand(-100, 100) / 10))), 2);

                // Gas level higher during busy market hours
                $baseGas = $hour >= 7 && $hour <= 19 ? 180 : 80;
                $gas     = round($baseGas + rand(-50, 120), 2);

                SensorReading::create([
                    'device_id'   => $device->id,
                    'stall_id'    => $device->stall_id,
                    'temperature' => $temp,
                    'humidity'    => $humidity,
                    'gas_level'   => max(0, $gas),
                    'recorded_at' => $current->toDateTimeString(),
                ]);

                $current->addMinutes(30);
            }
        }
    }
}
