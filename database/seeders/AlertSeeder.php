<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Device;
use App\Models\Market;
use App\Models\SensorReading;
use App\Models\Threshold;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AlertSeeder extends Seeder
{
    public function run(): void
    {
        $market    = Market::where('name', 'like', '%Nyarugenge%')->firstOrFail();
        $devices   = Device::where('market_id', $market->id)->where('is_active', true)->get();
        $inspector = User::where('role', 'inspector')->first();

        $scenarios = [
            ['parameter' => 'temperature', 'measured' => 38.5, 'threshold' => 35.0, 'severity' => 'warning',  'status' => 'open'],
            ['parameter' => 'gas_level',   'measured' => 520.0,'threshold' => 400.0, 'severity' => 'critical', 'status' => 'open'],
            ['parameter' => 'humidity',    'measured' => 85.0, 'threshold' => 80.0,  'severity' => 'warning',  'status' => 'acknowledged'],
            ['parameter' => 'temperature', 'measured' => 39.2, 'threshold' => 35.0,  'severity' => 'warning',  'status' => 'resolved'],
            ['parameter' => 'gas_level',   'measured' => 450.0,'threshold' => 400.0, 'severity' => 'warning',  'status' => 'open'],
            ['parameter' => 'temperature', 'measured' => 41.0, 'threshold' => 35.0,  'severity' => 'critical', 'status' => 'open'],
            ['parameter' => 'humidity',    'measured' => 90.0, 'threshold' => 80.0,  'severity' => 'critical', 'status' => 'acknowledged'],
            ['parameter' => 'gas_level',   'measured' => 380.0,'threshold' => 400.0, 'severity' => 'info',     'status' => 'resolved'],
        ];

        foreach ($devices->take(count($scenarios)) as $i => $device) {
            $s         = $scenarios[$i];
            $threshold = Threshold::where('market_id', $market->id)
                ->where('parameter', $s['parameter'])
                ->first();

            $reading = SensorReading::where('device_id', $device->id)->inRandomOrder()->first();

            $alertData = [
                'device_id'         => $device->id,
                'stall_id'          => $device->stall_id,
                'sensor_reading_id' => $reading?->id,
                'threshold_id'      => $threshold?->id,
                'parameter'         => $s['parameter'],
                'measured_value'    => $s['measured'],
                'threshold_value'   => $s['threshold'],
                'severity'          => $s['severity'],
                'message'           => ucfirst(str_replace('_', ' ', $s['parameter']))
                                     . ' reading of ' . $s['measured']
                                     . ' exceeded the safety threshold of ' . $s['threshold'] . '.',
                'status'            => $s['status'],
                'created_at'        => Carbon::now()->subHours(rand(1, 96)),
            ];

            if (in_array($s['status'], ['acknowledged', 'resolved'])) {
                $alertData['acknowledged_by'] = $inspector?->id;
                $alertData['acknowledged_at'] = Carbon::now()->subHours(rand(1, 48));
            }

            if ($s['status'] === 'resolved') {
                $alertData['resolved_by'] = $inspector?->id;
                $alertData['resolved_at'] = Carbon::now()->subHours(rand(1, 24));
            }

            Alert::create($alertData);
        }
    }
}
