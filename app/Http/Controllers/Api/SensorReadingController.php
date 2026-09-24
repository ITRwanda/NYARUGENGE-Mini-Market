<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Device;
use App\Models\SensorReading;
use App\Models\Threshold;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SensorReadingController extends Controller
{
    /*─────────────────────────────────────────────────────────────
     | ESP32 DATA INGESTION
     |
     | POST /api/iot/readings
     |
     | Payload (JSON):
     |   device_uid   string  required   Must match a registered device
     |   temperature  float   optional   °C
     |   humidity     float   optional   % (0–100)
     |   gas_level    float   optional   ppm (≥ 0)
     |   recorded_at  string  optional   ISO datetime, defaults to now()
     |
     | On success:
     |   - Saves the reading
     |   - Updates device status → online + last_seen_at
     |   - Auto-creates alerts for any threshold breaches
     |   - Returns HTTP 201
     *────────────────────────────────────────────────────────────*/
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_uid'  => 'required|string|exists:devices,device_uid',
            'temperature' => 'nullable|numeric|between:-40,125',
            'humidity'    => 'nullable|numeric|min:0|max:100',
            'gas_level'   => 'nullable|numeric|min:0',
            'recorded_at' => 'nullable|date',
        ]);

        return DB::transaction(function () use ($validated) {

            $device = Device::where('device_uid', $validated['device_uid'])
                ->firstOrFail();

            $reading = SensorReading::create([
                'device_id'   => $device->id,
                'stall_id'    => $device->stall_id,
                'temperature' => $validated['temperature'] ?? null,
                'humidity'    => $validated['humidity']    ?? null,
                'gas_level'   => $validated['gas_level']   ?? null,
                'recorded_at' => $validated['recorded_at'] ?? now(),
            ]);

            $device->update([
                'last_seen_at' => now(),
                'status'       => 'online',
            ]);

            $this->checkThresholds($device, $reading);

            return response()->json([
                'success' => true,
                'message' => 'Sensor reading received.',
                'data'    => [
                    'id'          => $reading->id,
                    'device_uid'  => $device->device_uid,
                    'stall_id'    => $reading->stall_id,
                    'temperature' => $reading->temperature,
                    'humidity'    => $reading->humidity,
                    'gas_level'   => $reading->gas_level,
                    'recorded_at' => $reading->recorded_at,
                ],
            ], 201);
        });
    }

    /*─────────────────────────────────────────────────────────────
     | GET /api/devices/{device}/readings
     *────────────────────────────────────────────────────────────*/
    public function deviceReadings(Request $request, Device $device): JsonResponse
    {
        $query = $device->sensorReadings()->latest('recorded_at');

        if ($request->filled('from')) {
            $query->where('recorded_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('recorded_at', '<=', $request->to);
        }

        return response()->json([
            'success' => true,
            'data'    => $query->paginate($request->integer('per_page', 50)),
        ]);
    }

    /*─────────────────────────────────────────────────────────────
     | GET /api/readings/latest — one reading per device
     *────────────────────────────────────────────────────────────*/
    public function latest(): JsonResponse
    {
        $devices = Device::with(['stall'])
            ->where('is_active', true)
            ->get()
            ->map(function ($device) {
                $reading = $device->sensorReadings()
                    ->latest('recorded_at')
                    ->first();

                return [
                    'device_uid'   => $device->device_uid,
                    'device_name'  => $device->device_name,
                    'stall'        => $device->stall?->stall_number,
                    'status'       => $device->status,
                    'last_seen_at' => $device->last_seen_at,
                    'latest_reading' => $reading ? [
                        'temperature' => $reading->temperature,
                        'humidity'    => $reading->humidity,
                        'gas_level'   => $reading->gas_level,
                        'recorded_at' => $reading->recorded_at,
                    ] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $devices,
        ]);
    }

    /*─────────────────────────────────────────────────────────────
     | THRESHOLD CHECKING — creates alerts with smart severity
     *────────────────────────────────────────────────────────────*/
    private function checkThresholds(Device $device, SensorReading $reading): void
    {
        $thresholds = Threshold::where('market_id', $device->market_id)
            ->where(function ($q) use ($device) {
                $q->whereNull('device_id')->orWhere('device_id', $device->id);
            })
            ->where('is_active', true)
            ->get();

        foreach ($thresholds as $threshold) {

            $value = match ($threshold->parameter) {
                'temperature' => $reading->temperature,
                'humidity'    => $reading->humidity,
                'gas_level'   => $reading->gas_level,
                default       => null,
            };

            if ($value === null) {
                continue;
            }

            $breached  = false;
            $direction = '';

            if ($threshold->minimum_value !== null && $value < $threshold->minimum_value) {
                $breached  = true;
                $direction = 'below minimum';
            }

            if ($threshold->maximum_value !== null && $value > $threshold->maximum_value) {
                $breached  = true;
                $direction = 'above maximum';
            }

            if (! $breached) {
                continue;
            }

            // Skip if an open/acknowledged alert already exists for same device+parameter
            $exists = Alert::where('device_id', $device->id)
                ->where('parameter', $threshold->parameter)
                ->whereIn('status', ['open', 'acknowledged'])
                ->exists();

            if ($exists) {
                continue;
            }

            // Smart severity: how far is the breach?
            $limit    = $threshold->maximum_value ?? $threshold->minimum_value;
            $overshoot = abs($value - $limit);
            $severity  = match (true) {
                $overshoot > ($limit * 0.20) => 'critical',
                $overshoot > ($limit * 0.05) => 'warning',
                default                      => 'info',
            };

            Alert::create([
                'device_id'         => $device->id,
                'stall_id'          => $device->stall_id,
                'sensor_reading_id' => $reading->id,
                'threshold_id'      => $threshold->id,
                'parameter'         => $threshold->parameter,
                'measured_value'    => $value,
                'threshold_value'   => $limit,
                'severity'          => $severity,
                'message'           => sprintf(
                    '%s reading of %s%s is %s threshold (%s%s).',
                    ucfirst(str_replace('_', ' ', $threshold->parameter)),
                    $value,
                    $threshold->unit ?? '',
                    $direction,
                    $limit,
                    $threshold->unit ?? ''
                ),
                'status' => 'open',
            ]);
        }
    }
}
