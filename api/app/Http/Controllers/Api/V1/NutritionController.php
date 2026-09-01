<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NutritionController extends Controller
{
    /**
     * Search for food items using USDA FoodData Central API
     */
    public function searchFood(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:200',
        ]);

        $apiKey = env('USDA_API_KEY');
        
        // Check if API key exists
        if (empty($apiKey)) {
            return response()->json([
                'status' => 'error',
                'message' => 'USDA API key not configured',
            ], 500);
        }

        try {
            // Make request to USDA API
            $response = Http::withOptions([
                'verify' => false, // Fix SSL issues
            ])
            ->timeout(15)
            ->get('https://api.nal.usda.gov/fdc/v1/foods/search', [
                'query' => $request->input('query'),
                'api_key' => $apiKey,
                'pageSize' => 20,
                'dataType' => 'Survey (FNDDS),SR Legacy,Foundation',
            ]);

            // Log the response status for debugging
            Log::info('USDA API Response Status: ' . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                
                if (!isset($data['foods']) || empty($data['foods'])) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'No results found',
                        'foods' => [],
                    ]);
                }

                $foods = collect($data['foods'])->map(function ($food) {
                    // Initialize nutrients array
                    $nutrients = [
                        'calories' => 0,
                        'protein' => 0,
                        'carbohydrates' => 0,
                        'fat' => 0,
                        'fiber' => 0,
                        'sugar' => 0,
                    ];
                    
                    // Extract nutrients from foodNutrients array
                    foreach ($food['foodNutrients'] as $nutrient) {
                        $name = $nutrient['nutrientName'] ?? '';
                        $value = $nutrient['value'] ?? 0;
                        
                        switch ($name) {
                            case 'Energy':
                                $nutrients['calories'] = round($value);
                                break;
                            case 'Protein':
                                $nutrients['protein'] = round($value, 1);
                                break;
                            case 'Carbohydrate, by difference':
                                $nutrients['carbohydrates'] = round($value, 1);
                                break;
                            case 'Total lipid (fat)':
                                $nutrients['fat'] = round($value, 1);
                                break;
                            case 'Fiber, total dietary':
                                $nutrients['fiber'] = round($value, 1);
                                break;
                            case 'Sugars, total including NLEA':
                                $nutrients['sugar'] = round($value, 1);
                                break;
                        }
                    }

                    return [
                        'name' => $food['description'] ?? 'Unknown',
                        'brand' => $food['brandOwner'] ?? $food['brandName'] ?? 'Generic',
                        'nutrients' => $nutrients,
                    ];
                })
                ->filter(function ($food) {
                    // Only include foods with calories
                    return $food['nutrients']['calories'] > 0;
                })
                ->values();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Food search results',
                    'foods' => $foods,
                ]);
            }

            // Log error details
            Log::error('USDA API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'USDA API error: ' . $response->status(),
            ], 502);
            
        } catch (\Exception $e) {
            Log::error('Food search exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error searching for food: ' . $e->getMessage(),
            ], 500);
        }
    }
}