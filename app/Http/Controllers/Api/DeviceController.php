<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = Device::with([
            'market',
            'stall'
        ]);

        if ($request->filled('market_id')) {
            $query->where('market_id', $request->market_id);
        }

        if ($request->filled('stall_id')) {
            $query->where('stall_id', $request->stall_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $devices = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Devices retrieved successfully.',
            'data' => $devices
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'market_id' => 'required|exists:markets,id',
            'stall_id' => 'nullable|exists:stalls,id',
            'device_uid' => 'required|string|max:255|unique:devices,device_uid',
            'device_name' => 'nullable|string|max:255',
            'microcontroller' => 'nullable|string|max:100',
            'communication_module' => 'nullable|string|max:100',
            'temperature_sensor' => 'nullable|string|max:100',
            'humidity_sensor' => 'nullable|string|max:100',
            'gas_sensor' => 'nullable|string|max:100',
            'firmware_version' => 'nullable|string|max:100',
            'status' => 'nullable|in:online,offline,maintenance,inactive',
            'is_active' => 'nullable|boolean',
        ]);

        $device = Device::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Device registered successfully.',
            'data' => $device
        ], 201);
    }

    public function show(Device $device)
    {
        $device->load([
            'market',
            'stall'
        ]);

        return response()->json([
            'success' => true,
            'data' => $device
        ]);
    }

    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'market_id' => 'sometimes|required|exists:markets,id',
            'stall_id' => 'nullable|exists:stalls,id',
            'device_uid' => 'sometimes|required|string|max:255|unique:devices,device_uid,' . $device->id,
            'device_name' => 'nullable|string|max:255',
            'microcontroller' => 'nullable|string|max:100',
            'communication_module' => 'nullable|string|max:100',
            'temperature_sensor' => 'nullable|string|max:100',
            'humidity_sensor' => 'nullable|string|max:100',
            'gas_sensor' => 'nullable|string|max:100',
            'firmware_version' => 'nullable|string|max:100',
            'status' => 'nullable|in:online,offline,maintenance,inactive',
            'is_active' => 'nullable|boolean',
        ]);

        $device->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Device updated successfully.',
            'data' => $device
        ]);
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return response()->json([
            'success' => true,
            'message' => 'Device deleted successfully.'
        ]);
    }
}