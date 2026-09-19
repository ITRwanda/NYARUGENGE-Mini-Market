<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stall;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StallController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Stall::with([
            'market',
            'vendor',
            'devices'
        ]);

        if ($request->filled('market_id')) {
            $query->where('market_id', $request->market_id);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $stalls = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Stalls retrieved successfully.',
            'data' => $stalls
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'market_id' => 'required|exists:markets,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'stall_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('stalls')
                    ->where(fn ($query) =>
                        $query->where('market_id', $request->market_id)
                    ),
            ],
            'section' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $stall = Stall::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Stall created successfully.',
            'data' => $stall
        ], 201);
    }

    public function show(Stall $stall): JsonResponse
    {
        $stall->load([
            'market',
            'vendor',
            'devices'
        ]);

        return response()->json([
            'success' => true,
            'data' => $stall
        ]);
    }

    public function update(Request $request, Stall $stall): JsonResponse
    {
        $validated = $request->validate([
            'market_id' => 'sometimes|required|exists:markets,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'stall_number' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('stalls')
                    ->where(fn ($query) =>
                        $query->where(
                            'market_id',
                            $request->market_id ?? $stall->market_id
                        )
                    )
                    ->ignore($stall->id),
            ],
            'section' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $stall->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Stall updated successfully.',
            'data' => $stall
        ]);
    }

    public function destroy(Stall $stall): JsonResponse
    {
        $stall->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stall deleted successfully.'
        ]);
    }
}