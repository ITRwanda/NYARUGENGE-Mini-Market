<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MarketAdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.login'));

/*─── Public auth ──────────────────────────────────────────────*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',  [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
});

/*─── Authenticated — all roles go here ───────────────────────*/
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    /*── All roles ─────────────────────────────────────────────*/
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile',          [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    /*── Admin only ─────────────────────────────────────────────
     | All management routes. Controllers enforce restrictions.
     *─────────────────────────────────────────────────────────*/
    Route::middleware('role:admin')->group(function () {

        // Market
        Route::get('/markets',               [MarketAdminController::class, 'markets'])->name('markets');
        Route::get('/markets/create',        [MarketAdminController::class, 'createMarket'])->name('markets.create');
        Route::post('/markets',              [MarketAdminController::class, 'storeMarket'])->name('markets.store');
        Route::get('/markets/{market}/edit', [MarketAdminController::class, 'editMarket'])->name('markets.edit');
        Route::put('/markets/{market}',      [MarketAdminController::class, 'updateMarket'])->name('markets.update');
        Route::delete('/markets/{market}',   [MarketAdminController::class, 'destroyMarket'])->name('markets.destroy');

        // Vendors
        Route::get('/vendors',               [MarketAdminController::class, 'vendors'])->name('vendors');
        Route::get('/vendors/create',        [MarketAdminController::class, 'createVendor'])->name('vendors.create');
        Route::post('/vendors',              [MarketAdminController::class, 'storeVendor'])->name('vendors.store');
        Route::get('/vendors/{vendor}/edit', [MarketAdminController::class, 'editVendor'])->name('vendors.edit');
        Route::put('/vendors/{vendor}',      [MarketAdminController::class, 'updateVendor'])->name('vendors.update');
        Route::delete('/vendors/{vendor}',   [MarketAdminController::class, 'destroyVendor'])->name('vendors.destroy');

        // Stalls
        Route::get('/stalls',               [MarketAdminController::class, 'stalls'])->name('stalls');
        Route::get('/stalls/create',        [MarketAdminController::class, 'createStall'])->name('stalls.create');
        Route::post('/stalls',              [MarketAdminController::class, 'storeStall'])->name('stalls.store');
        Route::get('/stalls/{stall}/edit',  [MarketAdminController::class, 'editStall'])->name('stalls.edit');
        Route::put('/stalls/{stall}',       [MarketAdminController::class, 'updateStall'])->name('stalls.update');
        Route::delete('/stalls/{stall}',    [MarketAdminController::class, 'destroyStall'])->name('stalls.destroy');

        // Devices
        Route::get('/devices',               [MarketAdminController::class, 'devices'])->name('devices');
        Route::get('/devices/create',        [MarketAdminController::class, 'createDevice'])->name('devices.create');
        Route::post('/devices',              [MarketAdminController::class, 'storeDevice'])->name('devices.store');
        Route::get('/devices/{device}/edit', [MarketAdminController::class, 'editDevice'])->name('devices.edit');
        Route::put('/devices/{device}',      [MarketAdminController::class, 'updateDevice'])->name('devices.update');
        Route::delete('/devices/{device}',   [MarketAdminController::class, 'destroyDevice'])->name('devices.destroy');

        // Thresholds
        Route::get('/thresholds',                  [MarketAdminController::class, 'thresholds'])->name('thresholds');
        Route::get('/thresholds/create',           [MarketAdminController::class, 'createThreshold'])->name('thresholds.create');
        Route::post('/thresholds',                 [MarketAdminController::class, 'storeThreshold'])->name('thresholds.store');
        Route::get('/thresholds/{threshold}/edit', [MarketAdminController::class, 'editThreshold'])->name('thresholds.edit');
        Route::put('/thresholds/{threshold}',      [MarketAdminController::class, 'updateThreshold'])->name('thresholds.update');
        Route::delete('/thresholds/{threshold}',   [MarketAdminController::class, 'destroyThreshold'])->name('thresholds.destroy');

        // Inspector management
        Route::get('/inspectors',                      [UserManagementController::class, 'index'])->name('users');
        Route::get('/inspectors/create',               [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/inspectors',                     [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/inspectors/{user}/edit',          [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/inspectors/{user}',               [UserManagementController::class, 'update'])->name('users.update');
        Route::patch('/inspectors/{user}/lock',        [UserManagementController::class, 'lock'])->name('users.lock');
        Route::patch('/inspectors/{user}/unlock',      [UserManagementController::class, 'unlock'])->name('users.unlock');
        Route::delete('/inspectors/{user}',            [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::get('/inspectors/{user}/assign-stalls', [UserManagementController::class, 'assignStalls'])->name('users.assign-stalls');
        Route::post('/inspectors/{user}/assign-stalls',[UserManagementController::class, 'saveStalls'])->name('users.save-stalls');
    });

    /*── Admin + Inspector — SHARED route names ─────────────────
     |
     | Both roles access the SAME URL and SAME route name.
     | Role enforcement is handled inside each controller method:
     |   - Admin:     sees everything, read-only on inspections
     |   - Inspector: sees only their own data, full CRUD on inspections
     |
     *─────────────────────────────────────────────────────────*/
    Route::middleware('role:admin,inspector')->group(function () {

        // Sensor readings
        Route::get('/sensor-readings', [MarketAdminController::class, 'sensorReadings'])
            ->name('sensor-readings');

        // Alerts — both roles see alerts; controller filters by role
        Route::get('/alerts',                       [MarketAdminController::class, 'alerts'])
            ->name('alerts');
        Route::patch('/alerts/{alert}/acknowledge', [MarketAdminController::class, 'acknowledgeAlert'])
            ->name('alerts.acknowledge');
        Route::patch('/alerts/{alert}/resolve',     [MarketAdminController::class, 'resolveAlert'])
            ->name('alerts.resolve');

        // Inspections — same URL, controller enforces who can write
        Route::get('/inspections',                   [MarketAdminController::class, 'inspections'])
            ->name('inspections');
        Route::get('/inspections/create',            [MarketAdminController::class, 'createInspection'])
            ->name('inspections.create');
        Route::post('/inspections',                  [MarketAdminController::class, 'storeInspection'])
            ->name('inspections.store');
        Route::get('/inspections/{inspection}',      [MarketAdminController::class, 'showInspection'])
            ->name('inspections.show');
        Route::get('/inspections/{inspection}/edit', [MarketAdminController::class, 'editInspection'])
            ->name('inspections.edit');
        Route::put('/inspections/{inspection}',      [MarketAdminController::class, 'updateInspection'])
            ->name('inspections.update');
        Route::delete('/inspections/{inspection}',   [MarketAdminController::class, 'destroyInspection'])
            ->name('inspections.destroy');

        // Reports
        Route::get('/reports',              [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/export/pdf',   [ReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
        Route::get('/reports/export/csv',   [ReportController::class, 'exportCsv'])->name('reports.csv');
    });

    /*── Vendor portal ──────────────────────────────────────────*/
    Route::middleware('role:vendor')->group(function () {
        Route::get('/my-stall',                        [MarketAdminController::class, 'myStall'])->name('my-stall');
        Route::get('/my-alerts',                       [MarketAdminController::class, 'myAlerts'])->name('my-alerts');
        Route::get('/my-readings',                     [MarketAdminController::class, 'myReadings'])->name('my-readings');
        Route::get('/my-inspections',                  [MarketAdminController::class, 'myInspections'])->name('my-inspections');
        Route::get('/my-inspections/{inspection}',     [MarketAdminController::class, 'myInspectionShow'])->name('my-inspections.show');
    });
});
