<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $query = Alert::with([
            'device',
            'stall',
            'sensorReading',
            'threshold'
        ]);

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('severity')) {
            $query->where(
                'severity',
                $request->severity
            );
        }

        if ($request->filled('device_id')) {
            $query->where(
                'device_id',
                $request->device_id
            );
        }

        $alerts = $query
            ->latest()
            ->paginate(
                $request->integer('per_page', 20)
            );

        return response()->json([
            'success' => true,
            'data' => $alerts
        ]);
    }

    public function show(Alert $alert)
    {
        return response()->json([
            'success' => true,
            'data' => $alert->load([
                'device',
                'stall',
                'sensorReading',
                'threshold'
            ])
        ]);
    }

    public function acknowledge(Alert $alert)
    {
        $alert->update([
            'status' => 'acknowledged',
            'acknowledged_by' => auth()->id(),
            'acknowledged_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alert acknowledged successfully.',
            'data' => $alert
        ]);
    }

    public function resolve(Alert $alert)
    {
        $alert->update([
            'status' => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alert resolved successfully.',
            'data' => $alert
        ]);
    }
}