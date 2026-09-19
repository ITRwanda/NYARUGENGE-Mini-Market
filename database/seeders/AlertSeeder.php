<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Device;
use App\Models\SensorReading;
use App\Models\Threshold;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AlertSeeder extends Seeder
{
    public function run(): void
    {
        $devices   = Device::where('is_active', true)->get();
        $admin     = User::where('role', 'admin')->first();
        $inspector = User::where('role', 'inspector')->first();

        $severities = ['info', 'warning', 'critical'];
        $statuses   = ['open', 'acknowledged', 'resolved'];

        $parameters = [
            'temperature' => ['value' => 38.5, 'threshold' => 35.0, 'unit' => '°C'],
            'humidity'    => ['value' => 88.0, 'threshold' => 80.0, 'unit' => '%'],
            'gas_level'   => ['value' => 520.0, 'threshold' => 400.0, 'unit' => 'ppm'],
        ];

        foreach ($devices->take(8) as $dIndex => $device) {
            $threshold = Threshold::where('market_id', $device->market_id)
                ->inRandomOrder()
                ->first();

            $reading = SensorReading::where('device_id', $device->id)
                ->inRandomOrder()
                ->first();

            $paramKey = array_keys($parameters)[$dIndex % 3];
            $param    = $parameters[$paramKey];

            $status   = $statuses[$dIndex % 3];
            $severity = $severities[$dIndex % 3];

            $alertData = [
                'device_id'        => $device->id,
                'stall_id'         => $device->stall_id,
                'sensor_reading_id'=> $reading?->id,
                'threshold_id'     => $threshold?->id,
                'parameter'        => $paramKey,
                'measured_value'   => $param['value'],
                'threshold_value'  => $param['threshold'],
                'severity'         => $severity,
                'message'          => ucfirst($paramKey) . ' has exceeded the configured safety threshold. Measured: ' . $param['value'] . $param['unit'] . ', Limit: ' . $param['threshold'] . $param['unit'],
                'status'           => $status,
                'created_at'       => Carbon::now()->subHours(rand(1, 72)),
            ];

            if ($status === 'acknowledged' || $status === 'resolved') {
                $alertData['acknowledged_by'] = $inspector?->id;
                $alertData['acknowledged_at'] = Carbon::now()->subHours(rand(1, 24));
            }

            if ($status === 'resolved') {
                $alertData['resolved_by'] = $admin?->id;
                $alertData['resolved_at'] = Carbon::now()->subHours(rand(1, 12));
            }

            Alert::create($alertData);
        }
    }
}
