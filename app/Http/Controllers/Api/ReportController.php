<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SensorReading;
use App\Models\Alert;
use App\Models\Inspection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->input(
            'date',
            now()->toDateString()
        );

        return $this->generateReport(
            Carbon::parse($date)->startOfDay(),
            Carbon::parse($date)->endOfDay()
        );
    }

    public function weekly(Request $request)
    {
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        return $this->generateReport($start, $end);
    }

    public function monthly(Request $request)
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        return $this->generateReport($start, $end);
    }

    private function generateReport(
        Carbon $start,
        Carbon $end
    ) {
        $readings = SensorReading::whereBetween(
            'recorded_at',
            [$start, $end]
        );

        $alerts = Alert::whereBetween(
            'created_at',
            [$start, $end]
        );

        $inspections = Inspection::whereBetween(
            'inspection_date',
            [$start, $end]
        );

        return response()->json([
            'success' => true,

            'period' => [
                'start' => $start,
                'end' => $end,
            ],

            'summary' => [
                'total_sensor_readings' => (clone $readings)->count(),

                'average_temperature' =>
                    (clone $readings)->avg('temperature'),

                'average_humidity' =>
                    (clone $readings)->avg('humidity'),

                'average_gas_level' =>
                    (clone $readings)->avg('gas_level'),

                'total_alerts' =>
                    (clone $alerts)->count(),

                'open_alerts' =>
                    (clone $alerts)
                        ->where('status', 'open')
                        ->count(),

                'critical_alerts' =>
                    (clone $alerts)
                        ->where('severity', 'critical')
                        ->count(),

                'total_inspections' =>
                    (clone $inspections)->count(),
            ]
        ]);
    }
}