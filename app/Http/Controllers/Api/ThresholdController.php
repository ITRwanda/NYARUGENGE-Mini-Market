<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Threshold;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThresholdController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Threshold::with([
            'market',
            'device'
        ]);

        if ($request->filled('market_id')) {
            $query->where(
                'market_id',
                $request->market_id
            );
        }

        if ($request->filled('parameter')) {
            $query->where(
                'parameter',
                $request->parameter
            );
        }

        $thresholds = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $thresholds
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'market_id' => 'required|exists:markets,id',
            'device_id' => 'nullable|exists:devices,id',
            'parameter' => 'required|in:temperature,humidity,gas_level',
            'minimum_value' => 'nullable|numeric',
            'maximum_value' => 'nullable|numeric',
            'unit' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        if (
            empty($validated['minimum_value']) &&
            empty($validated['maximum_value'])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'At least one threshold value is required.'
            ], 422);
        }

        $threshold = Threshold::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Threshold created successfully.',
            'data' => $threshold
        ], 201);
    }

    public function show(Threshold $threshold): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $threshold->load([
                'market',
                'device'
            ])
        ]);
    }

    public function update(
        Request $request,
        Threshold $threshold
    ): JsonResponse {
        $validated = $request->validate([
            'market_id' => 'sometimes|required|exists:markets,id',
            'device_id' => 'nullable|exists:devices,id',
            'parameter' => 'sometimes|required|in:temperature,humidity,gas_level',
            'minimum_value' => 'nullable|numeric',
            'maximum_value' => 'nullable|numeric',
            'unit' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $threshold->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Threshold updated successfully.',
            'data' => $threshold
        ]);
    }

    public function destroy(Threshold $threshold): JsonResponse
    {
        $threshold->delete();

        return response()->json([
            'success' => true,
            'message' => 'Threshold deleted successfully.'
        ]);
    }
}