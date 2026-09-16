<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::with([
            'user',
            'market',
            'stalls'
        ]);

        if ($request->filled('market_id')) {
            $query->where('market_id', $request->market_id);
        }

        $vendors = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Vendors retrieved successfully.',
            'data' => $vendors
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'market_id' => 'required|exists:markets,id',
            'vendor_code' => 'required|string|max:100|unique:vendors,vendor_code',
            'business_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'food_category' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $vendor = Vendor::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vendor created successfully.',
            'data' => $vendor->load(['user', 'market'])
        ], 201);
    }

    public function show(Vendor $vendor)
    {
        $vendor->load([
            'user',
            'market',
            'stalls',
            'stalls.devices'
        ]);

        return response()->json([
            'success' => true,
            'data' => $vendor
        ]);
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'market_id' => 'sometimes|required|exists:markets,id',
            'vendor_code' => 'sometimes|required|string|max:100|unique:vendors,vendor_code,' . $vendor->id,
            'business_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'food_category' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $vendor->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vendor updated successfully.',
            'data' => $vendor
        ]);
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vendor deleted successfully.'
        ]);
    }
}