<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLeagueRequest;
use App\Http\Resources\LeagueResource;
use App\Models\League;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeagueController extends Controller
{
    public function index(Request $request)
    {
        $query = League::with(['sport', 'country'])->withCount('matches');

        if ($request->has('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        $leagues = $query->orderBy('order')->paginate($request->get('per_page', 50));

        return LeagueResource::collection($leagues);
    }

    public function store(StoreLeagueRequest $request): JsonResponse
    {
        $league = League::create($request->validated());

        return response()->json([
            'message' => 'League created successfully.',
            'league' => new LeagueResource($league->load(['sport', 'country'])),
        ], 201);
    }

    public function show(League $league): JsonResponse
    {
        $league->load(['sport', 'country'])->loadCount('matches');

        return response()->json(['league' => new LeagueResource($league)]);
    }

    public function update(Request $request, League $league): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:leagues,slug,' . $league->id,
            'sport_id' => 'sometimes|exists:sports,id',
            'country_id' => 'sometimes|exists:countries,id',
            'is_active' => 'sometimes|boolean',
            'order' => 'sometimes|integer',
        ]);

        $league->update($validated);

        return response()->json([
            'message' => 'League updated successfully.',
            'league' => new LeagueResource($league->load(['sport', 'country'])),
        ]);
    }

    public function destroy(League $league): JsonResponse
    {
        if ($league->matches()->exists()) {
            return response()->json(['message' => 'Cannot delete league with associated matches.'], 422);
        }

        $league->delete();

        return response()->json(['message' => 'League deleted successfully.']);
    }
}
