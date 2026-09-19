<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // 👈 1. IMPORT THIS FIRST

class MarketController extends Controller
{
    public function index(): JsonResponse // 👈 2. ADD TYPE-HINT HERE
    {
        $markets = Market::withCount([
            'vendors',
            'stalls',
            'devices'
        ])->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Markets retrieved successfully.',
            'data' => $markets
        ]);
    }

    public function store(Request $request): JsonResponse // 👈 ADD TYPE-HINT HERE
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $market = Market::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Market created successfully.',
            'data' => $market
        ], 201);
    }

    public function show(Market $market): JsonResponse // 👈 ADD TYPE-HINT HERE
    {
        $market->load([
            'vendors',
            'stalls',
            'devices'
        ]);

        return response()->json([
            'success' => true,
            'data' => $market
        ]);
    }

    public function update(Request $request, Market $market): JsonResponse // 👈 ADD TYPE-HINT HERE
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $market->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Market updated successfully.',
            'data' => $market
        ]);
    }

    public function destroy(Market $market): JsonResponse // 👈 ADD TYPE-HINT HERE
    {
        $market->delete();

        return response()->json([
            'success' => true,
            'message' => 'Market deleted successfully.'
        ]);
    }
}
