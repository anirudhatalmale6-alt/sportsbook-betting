<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSportRequest;
use App\Http\Resources\SportResource;
use App\Models\Sport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SportController extends Controller
{
    public function index()
    {
        $sports = Sport::withCount('leagues')->orderBy('order')->get();
        return SportResource::collection($sports);
    }

    public function store(StoreSportRequest $request): JsonResponse
    {
        $sport = Sport::create($request->validated());

        return response()->json([
            'message' => 'Sport created successfully.',
            'sport' => new SportResource($sport),
        ], 201);
    }

    public function show(Sport $sport): JsonResponse
    {
        $sport->loadCount('leagues');
        return response()->json(['sport' => new SportResource($sport)]);
    }

    public function update(Request $request, Sport $sport): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:sports,slug,' . $sport->id,
            'icon' => 'nullable|string|max:255',
            'order' => 'sometimes|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        $sport->update($validated);

        return response()->json([
            'message' => 'Sport updated successfully.',
            'sport' => new SportResource($sport),
        ]);
    }

    public function destroy(Sport $sport): JsonResponse
    {
        if ($sport->leagues()->exists()) {
            return response()->json(['message' => 'Cannot delete sport with associated leagues.'], 422);
        }

        $sport->delete();

        return response()->json(['message' => 'Sport deleted successfully.']);
    }
}
