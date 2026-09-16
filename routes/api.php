<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\MarketController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\StallController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\SensorReadingController;
use App\Http\Controllers\Api\ThresholdController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\InspectionController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AuthController;

Route::prefix('auth')->group(function () {

    Route::post(
        '/register',
        [AuthController::class, 'register']
    );

    Route::post(
        '/login',
        [AuthController::class, 'login']
    );
});

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('auth')->group(function () {

        Route::get(
            '/me',
            [AuthController::class, 'me']
        );

        Route::post(
            '/logout',
            [AuthController::class, 'logout']
        );

        Route::post(
            '/change-password',
            [AuthController::class, 'changePassword']
        );
    });
});

Route::prefix('iot')->group(function () {

    Route::post(
        '/readings',
        [SensorReadingController::class, 'store']
    );
});

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource(
        'markets',
        MarketController::class
    );

    Route::apiResource(
        'vendors',
        VendorController::class
    );

    Route::apiResource(
        'stalls',
        StallController::class
    );

    Route::apiResource(
        'devices',
        DeviceController::class
    );

    Route::get(
        'devices/{device}/readings',
        [SensorReadingController::class, 'deviceReadings']
    );

    Route::get(
        'readings/latest',
        [SensorReadingController::class, 'latest']
    );

    Route::apiResource(
        'thresholds',
        ThresholdController::class
    );

    Route::get(
        'alerts',
        [AlertController::class, 'index']
    );

    Route::get(
        'alerts/{alert}',
        [AlertController::class, 'show']
    );

    Route::patch(
        'alerts/{alert}/acknowledge',
        [AlertController::class, 'acknowledge']
    );

    Route::patch(
        'alerts/{alert}/resolve',
        [AlertController::class, 'resolve']
    );

    Route::apiResource(
        'inspections',
        InspectionController::class
    );

    Route::get(
        'reports/daily',
        [ReportController::class, 'daily']
    );

    Route::get(
        'reports/weekly',
        [ReportController::class, 'weekly']
    );

    Route::get(
        'reports/monthly',
        [ReportController::class, 'monthly']
    );
});