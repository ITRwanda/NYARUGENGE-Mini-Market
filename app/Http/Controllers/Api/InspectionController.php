<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InspectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Inspection::with([
            'market',
            'stall',
            'inspector',
            'items'
        ]);

        if ($request->filled('market_id')) {
            $query->where(
                'market_id',
                $request->market_id
            );
        }

        if ($request->filled('stall_id')) {
            $query->where(
                'stall_id',
                $request->stall_id
            );
        }

        $inspections = $query
            ->latest('inspection_date')
            ->paginate(
                $request->integer('per_page', 20)
            );

        return response()->json([
            'success' => true,
            'data' => $inspections
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'market_id' => 'required|exists:markets,id',
            'stall_id' => 'required|exists:stalls,id',
            'inspector_id' => 'required|exists:users,id',
            'inspection_date' => 'required|date',
            'status' => 'nullable|in:pending,completed,follow_up',
            'general_notes' => 'nullable|string',

            'items' => 'nullable|array',
            'items.*.item' => 'required|string|max:255',
            'items.*.status' => 'required|in:pass,fail,not_applicable',
            'items.*.notes' => 'nullable|string',
        ]);

        $inspection = DB::transaction(function () use ($validated) {

            $items = $validated['items'] ?? [];

            unset($validated['items']);

            $inspection = Inspection::create($validated);

            foreach ($items as $item) {
                $inspection->items()->create($item);
            }

            return $inspection;
        });

        return response()->json([
            'success' => true,
            'message' => 'Inspection created successfully.',
            'data' => $inspection->load('items')
        ], 201);
    }

    public function show(Inspection $inspection)
    {
        return response()->json([
            'success' => true,
            'data' => $inspection->load([
                'market',
                'stall',
                'inspector',
                'items'
            ])
        ]);
    }

    public function update(
        Request $request,
        Inspection $inspection
    ) {
        $validated = $request->validate([
            'market_id' => 'sometimes|required|exists:markets,id',
            'stall_id' => 'sometimes|required|exists:stalls,id',
            'inspector_id' => 'sometimes|required|exists:users,id',
            'inspection_date' => 'sometimes|required|date',
            'status' => 'nullable|in:pending,completed,follow_up',
            'general_notes' => 'nullable|string',

            'items' => 'nullable|array',
            'items.*.item' => 'required|string|max:255',
            'items.*.status' => 'required|in:pass,fail,not_applicable',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use (
            $validated,
            $inspection
        ) {
            $items = $validated['items'] ?? null;

            unset($validated['items']);

            $inspection->update($validated);

            if ($items !== null) {

                $inspection->items()->delete();

                foreach ($items as $item) {
                    $inspection->items()->create($item);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Inspection updated successfully.',
            'data' => $inspection->load('items')
        ]);
    }

    public function destroy(Inspection $inspection)
    {
        $inspection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inspection deleted successfully.'
        ]);
    }
}