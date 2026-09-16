<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Market;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function index()
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

    public function store(Request $request)
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

    public function show(Market $market)
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

    public function update(Request $request, Market $market)
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

    public function destroy(Market $market)
    {
        $market->delete();

        return response()->json([
            'success' => true,
            'message' => 'Market deleted successfully.'
        ]);
    }
}