<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SensorReadingController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\DeviceController;

/*
|==========================================================================
| NYARUGENGE MINI MARKET — REST API
|==========================================================================
|
| Base URL: /api
|
| ── IoT (public, no auth) ────────────────────────────────────────────────
|   POST /api/iot/readings          ESP32 posts sensor data here
|
| ── Auth ─────────────────────────────────────────────────────────────────
|   POST /api/auth/login            Get Bearer token
|   POST /api/auth/logout           Revoke token
|   GET  /api/auth/me               Authenticated user info
|
| ── Monitoring (requires Bearer token) ───────────────────────────────────
|   GET  /api/readings/latest       Latest reading per device
|   GET  /api/devices               List all devices + status
|   GET  /api/alerts                List alerts (filterable)
|
*/

/*─────────────────────────────────────────────────────────────────────────
 | IoT DATA INGESTION — public endpoint for ESP32 / ESP8266 devices
 |
 | The ESP32 should POST to: POST /api/iot/readings
 |
 | Required payload:
 |   {
 |     "device_uid":   "ESP32-A1B2C3",   // must match a registered device
 |     "temperature":  28.5,              // °C  (nullable)
 |     "humidity":     65.2,              // %   (nullable, 0-100)
 |     "gas_level":    210.0             // ppm (nullable)
 |   }
 |
 | Optional:
 |   "recorded_at": "2026-09-23 14:00:00"  // defaults to now()
 |
 | Response 201:
 |   { "success": true, "message": "...", "data": { reading object } }
 |
 | Response 422: validation error (unknown device_uid, out-of-range values)
 |
 | Security: No auth required — device is identified by device_uid.
 |           All device_uids are registered by the admin in the dashboard.
 *────────────────────────────────────────────────────────────────────────*/
Route::prefix('iot')->group(function () {

    // Browser health-check — open this URL to confirm the API is reachable
    Route::get('/readings', function () {
        return response()->json([
            'status'   => 'ok',
            'endpoint' => 'POST /api/iot/readings',
            'message'  => 'Send a POST request with JSON body to submit sensor data.',
            'example'  => [
                'device_uid'  => 'ESP32-A1B2C3',
                'temperature' => 28.5,
                'humidity'    => 65.2,
                'gas_level'   => 210.0,
            ],
            'registered_devices' => \App\Models\Device::select('device_uid', 'device_name', 'status')
                ->where('is_active', true)
                ->orderBy('device_name')
                ->get(),
        ]);
    });

    Route::post('/readings', [SensorReadingController::class, 'store']);
});

/*─────────────────────────────────────────────────────────────────────────
 | AUTHENTICATION
 *────────────────────────────────────────────────────────────────────────*/
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::get('/me',     [AuthController::class, 'me']);
    Route::post('/logout',[AuthController::class, 'logout']);
});

/*─────────────────────────────────────────────────────────────────────────
 | MONITORING — authenticated endpoints for dashboard / mobile app
 *────────────────────────────────────────────────────────────────────────*/
Route::middleware('auth:sanctum')->group(function () {

    // Latest reading from every device (dashboard live view)
    Route::get('/readings/latest', [SensorReadingController::class, 'latest']);

    // Readings for a specific device
    Route::get('/devices/{device}/readings', [SensorReadingController::class, 'deviceReadings']);

    // Device list with status
    Route::get('/devices', [DeviceController::class, 'index']);

    // Alerts
    Route::get('/alerts',                     [AlertController::class, 'index']);
    Route::get('/alerts/{alert}',             [AlertController::class, 'show']);
    Route::patch('/alerts/{alert}/acknowledge',[AlertController::class, 'acknowledge']);
    Route::patch('/alerts/{alert}/resolve',   [AlertController::class, 'resolve']);
});
