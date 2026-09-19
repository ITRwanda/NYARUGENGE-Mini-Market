<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Stall;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $stalls    = Stall::all();
        $statuses  = ['online', 'online', 'online', 'offline', 'maintenance'];
        $firmwares = ['1.0.0', '1.1.0', '1.2.0', '2.0.0'];

        foreach ($stalls as $index => $stall) {
            $status = $statuses[$index % count($statuses)];

            Device::create([
                'market_id'            => $stall->market_id,
                'stall_id'             => $stall->id,
                'device_uid'           => 'ESP-' . strtoupper(bin2hex(random_bytes(4))),
                'device_name'          => 'Sensor Unit ' . ($index + 1),
                'microcontroller'      => 'Arduino Uno',
                'communication_module' => 'ESP8266',
                'temperature_sensor'   => 'DHT22',
                'humidity_sensor'      => 'DHT22',
                'gas_sensor'           => 'MQ-135',
                'firmware_version'     => $firmwares[$index % count($firmwares)],
                'last_seen_at'         => $status === 'online' ? now()->subMinutes(rand(1, 15)) : now()->subHours(rand(1, 48)),
                'status'               => $status,
                'is_active'            => $status !== 'inactive',
            ]);
        }
    }
}
