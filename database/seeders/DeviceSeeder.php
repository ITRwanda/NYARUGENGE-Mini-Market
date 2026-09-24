<?php

namespace Database\Seeders;

use App\Models\Market;
use App\Models\Device;
use App\Models\Stall;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $market   = Market::where('name', 'like', '%Nyarugenge%')->firstOrFail();
        $stalls   = Stall::where('market_id', $market->id)->orderBy('id')->get();

        $statuses  = ['online', 'online', 'online', 'online', 'offline', 'maintenance'];
        $firmwares = ['2.1.0', '2.1.0', '2.0.1', '2.0.1', '2.1.0', '1.9.5', '2.0.1'];

        foreach ($stalls as $index => $stall) {
            $status = $statuses[$index % count($statuses)];

            Device::create([
                'market_id'            => $market->id,
                'stall_id'             => $stall->id,
                'device_uid'           => 'ESP32-' . strtoupper(bin2hex(random_bytes(3))),
                'device_name'          => 'Sensor #' . str_pad($index + 1, 2, '0', STR_PAD_LEFT) . ' — ' . $stall->stall_number,
                'microcontroller'      => 'ESP32',
                'communication_module' => 'Wi-Fi 802.11 b/g/n',
                'temperature_sensor'   => 'DHT22',
                'humidity_sensor'      => 'DHT22',
                'gas_sensor'           => 'MQ-135',
                'firmware_version'     => $firmwares[$index % count($firmwares)],
                'last_seen_at'         => $status === 'online'
                    ? now()->subMinutes(rand(1, 10))
                    : now()->subHours(rand(2, 72)),
                'status'               => $status,
                'is_active'            => $status !== 'inactive',
            ]);
        }
    }
}
