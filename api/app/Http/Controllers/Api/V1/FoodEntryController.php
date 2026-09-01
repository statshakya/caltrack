<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FoodEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FoodEntryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $entries = FoodEntry::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totals = [
            'calories' => $entries->sum('calories'),
            'protein' => round($entries->sum('protein'), 1),
            'carbohydrates' => round($entries->sum('carbohydrates'), 1),
            'fat' => round($entries->sum('fat'), 1),
        ];
        
        return response()->json([
            'status' => 'success',
            'message' => 'Food entries retrieved successfully',
            'data' => [
                'entries' => $entries,
                'totals' => $totals,
                'count' => $entries->count(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'food_name' => 'required|string|max:255',
            'calories' => 'required|integer|min:0|max:10000',
            'entry_date' => 'required|date',
            'protein' => 'nullable|numeric|min:0|max:1000',
            'carbohydrates' => 'nullable|numeric|min:0|max:1000',
            'fat' => 'nullable|numeric|min:0|max:1000',
            'fiber' => 'nullable|numeric|min:0|max:100',
            'sugar' => 'nullable|numeric|min:0|max:500',
            'serving_size' => 'nullable|numeric|min:0|max:10000',
            'serving_unit' => 'nullable|string|max:20',
        ]);
        
        $user = $request->user();
        
        $entry = FoodEntry::create([
            'user_id' => $user->id,
            'food_name' => $validated['food_name'],
            'calories' => $validated['calories'],
            'entry_date' => $validated['entry_date'],
            'protein' => $validated['protein'] ?? null,
            'carbohydrates' => $validated['carbohydrates'] ?? null,
            'fat' => $validated['fat'] ?? null,
            'fiber' => $validated['fiber'] ?? null,
            'sugar' => $validated['sugar'] ?? null,
            'serving_size' => $validated['serving_size'] ?? 100,
            'serving_unit' => $validated['serving_unit'] ?? 'g',
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Food entry created successfully',
            'data' => [
                'entry' => $entry
            ]
        ], 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $entry = FoodEntry::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        
        if (!$entry) {
            return response()->json([
                'status' => 'error',
                'message' => 'Food entry not found'
            ], 404);
        }
        
        $entry->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Food entry deleted successfully'
        ]);
    }
}