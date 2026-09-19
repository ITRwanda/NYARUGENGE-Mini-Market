<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Device;
use App\Models\Inspection;
use App\Models\Market;
use App\Models\SensorReading;
use App\Models\Stall;
use App\Models\Vendor;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Route to role-specific dashboard
        if ($user->isVendor()) {
            return $this->vendorDashboard($user);
        }

        if ($user->isInspector()) {
            return $this->inspectorDashboard($user);
        }

        // Admin & Market Admin get the full dashboard
        return $this->adminDashboard();
    }

    /*------------------------------------------------------------------
     | ADMIN / MARKET ADMIN DASHBOARD
     *-----------------------------------------------------------------*/
    private function adminDashboard()
    {
        $stats = [
            'markets'         => Market::where('is_active', true)->count(),
            'vendors'         => Vendor::where('is_active', true)->count(),
            'stalls'          => Stall::where('is_active', true)->count(),
            'devices'         => Device::where('is_active', true)->count(),
            'devices_online'  => Device::where('status', 'online')->count(),
            'devices_offline' => Device::where('status', 'offline')->count(),
            'open_alerts'     => Alert::where('status', 'open')->count(),
            'critical_alerts' => Alert::where('severity', 'critical')->where('status', 'open')->count(),
            'total_readings'  => SensorReading::count(),
            'inspections_today' => Inspection::whereDate('inspection_date', today())->count(),
        ];

        $recent = SensorReading::where('recorded_at', '>=', now()->subHours(24));
        $stats['avg_temp']     = round((clone $recent)->avg('temperature') ?? 0, 1);
        $stats['avg_humidity'] = round((clone $recent)->avg('humidity') ?? 0, 1);
        $stats['avg_gas']      = round((clone $recent)->avg('gas_level') ?? 0, 1);

        // 7-day sensor trend
        $sensorChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->toDateString();
            $row = SensorReading::whereDate('recorded_at', $day)
                ->selectRaw('AVG(temperature) as avg_temp, AVG(humidity) as avg_humidity, AVG(gas_level) as avg_gas')
                ->first();
            $sensorChart[] = [
                'label'       => Carbon::parse($day)->format('D d'),
                'temperature' => round($row->avg_temp ?? 0, 1),
                'humidity'    => round($row->avg_humidity ?? 0, 1),
                'gas_level'   => round($row->avg_gas ?? 0, 1),
            ];
        }

        // 7-day alert trend
        $alertChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->toDateString();
            $alertChart[] = [
                'label'    => Carbon::parse($day)->format('D d'),
                'open'     => Alert::whereDate('created_at', $day)->where('status', 'open')->count(),
                'resolved' => Alert::whereDate('created_at', $day)->where('status', 'resolved')->count(),
            ];
        }

        $markets = Market::withCount(['vendors', 'stalls', 'devices'])
            ->where('is_active', true)->get();

        $recentAlerts = Alert::with(['device', 'stall'])
            ->whereIn('status', ['open', 'acknowledged'])
            ->latest()->take(8)->get();

        $deviceStatus = [
            'online'      => Device::where('status', 'online')->count(),
            'offline'     => Device::where('status', 'offline')->count(),
            'maintenance' => Device::where('status', 'maintenance')->count(),
        ];

        $inspectionStats = [
            'completed' => Inspection::where('status', 'completed')->count(),
            'pending'   => Inspection::where('status', 'pending')->count(),
            'follow_up' => Inspection::where('status', 'follow_up')->count(),
        ];

        return view('admin.dashboard', compact(
            'stats', 'sensorChart', 'alertChart', 'markets',
            'recentAlerts', 'deviceStatus', 'inspectionStats'
        ));
    }

    /*------------------------------------------------------------------
     | INSPECTOR DASHBOARD
     *-----------------------------------------------------------------*/
    private function inspectorDashboard($user)
    {
        $myInspections = Inspection::where('inspector_id', $user->id);

        $stats = [
            'total_inspections'     => (clone $myInspections)->count(),
            'completed_inspections' => (clone $myInspections)->where('status', 'completed')->count(),
            'pending_inspections'   => (clone $myInspections)->where('status', 'pending')->count(),
            'follow_up_inspections' => (clone $myInspections)->where('status', 'follow_up')->count(),
        ];

        // Stalls I've inspected
        $inspectedStallIds = (clone $myInspections)->pluck('stall_id')->unique();
        $stats['open_alerts'] = Alert::whereIn('stall_id', $inspectedStallIds)
            ->where('status', 'open')->count();

        $recentInspections = Inspection::with(['market', 'stall', 'items'])
            ->where('inspector_id', $user->id)
            ->latest('inspection_date')
            ->take(10)
            ->get();

        $recentAlerts = Alert::with(['device', 'stall'])
            ->whereIn('stall_id', $inspectedStallIds)
            ->where('status', 'open')
            ->latest()->take(8)->get();

        // Inspection pass-rate chart (last 7)
        $inspectionChart = Inspection::with('items')
            ->where('inspector_id', $user->id)
            ->where('status', 'completed')
            ->latest('inspection_date')
            ->take(7)
            ->get()
            ->map(function ($ins) {
                $total  = $ins->items->count();
                $passed = $ins->items->where('status', 'pass')->count();
                return [
                    'label'    => $ins->inspection_date->format('M d'),
                    'pass_pct' => $total > 0 ? round(($passed / $total) * 100) : 0,
                ];
            })->reverse()->values();

        return view('admin.inspector-dashboard', compact(
            'stats', 'recentInspections', 'recentAlerts', 'inspectionChart'
        ));
    }

    /*------------------------------------------------------------------
     | VENDOR DASHBOARD
     *-----------------------------------------------------------------*/
    private function vendorDashboard($user)
    {
        $vendor = $user->vendor?->load(['market', 'stalls.devices']);

        if (! $vendor) {
            return view('admin.vendor.no-profile');
        }

        $stallIds  = $vendor->stalls->pluck('id');
        $deviceIds = $vendor->stalls->pluck('devices')->flatten()->pluck('id');

        $stats = [
            'stalls'      => $vendor->stalls->count(),
            'devices'     => $deviceIds->count(),
            'open_alerts' => Alert::whereIn('stall_id', $stallIds)->where('status', 'open')->count(),
        ];

        // Latest readings per device
        $latestReadings = SensorReading::with(['device', 'stall'])
            ->whereIn('device_id', $deviceIds)
            ->latest('recorded_at')
            ->take($deviceIds->count())
            ->get()
            ->unique('device_id');

        // 24h sensor chart for vendor's devices
        $sensorChart = [];
        for ($i = 23; $i >= 0; $i -= 3) {
            $hour = Carbon::now()->subHours($i);
            $row  = SensorReading::whereIn('device_id', $deviceIds)
                ->where('recorded_at', '>=', $hour->copy()->subHours(1.5))
                ->where('recorded_at', '<',  $hour->copy()->addHours(1.5))
                ->selectRaw('AVG(temperature) as t, AVG(humidity) as h, AVG(gas_level) as g')
                ->first();
            $sensorChart[] = [
                'label'       => $hour->format('H:i'),
                'temperature' => round($row->t ?? 0, 1),
                'humidity'    => round($row->h ?? 0, 1),
                'gas_level'   => round($row->g ?? 0, 1),
            ];
        }

        $recentAlerts = Alert::with(['device', 'stall'])
            ->whereIn('stall_id', $stallIds)
            ->latest()->take(6)->get();

        return view('admin.vendor-dashboard', compact(
            'vendor', 'stats', 'latestReadings', 'sensorChart', 'recentAlerts'
        ));
    }
}
