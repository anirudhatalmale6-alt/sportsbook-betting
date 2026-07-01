<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMatchRequest;
use App\Http\Requests\Admin\UpdateMatchRequest;
use App\Http\Requests\Admin\UpdateOddsRequest;
use App\Http\Resources\MatchResource;
use App\Models\GameMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
    public function store(StoreMatchRequest $request): JsonResponse
    {
        $match = GameMatch::create($request->validated());
        $match->refresh();

        return response()->json([
            'message' => 'Match created successfully.',
            'match' => new MatchResource($match->load('league')),
        ], 201);
    }

    public function update(UpdateMatchRequest $request, int $id): JsonResponse
    {
        $match = GameMatch::findOrFail($id);
        $match->update($request->validated());

        return response()->json([
            'message' => 'Match updated successfully.',
            'match' => new MatchResource($match->load('league')),
        ]);
    }

    public function updateOdds(UpdateOddsRequest $request, int $id): JsonResponse
    {
        $match = GameMatch::findOrFail($id);

        DB::transaction(function () use ($match, $request) {
            // Remove existing odds first (to avoid FK constraint issues), then markets
            foreach ($match->markets as $market) {
                $market->odds()->delete();
            }
            $match->markets()->delete();

            foreach ($request->markets as $marketData) {
                $market = $match->markets()->create([
                    'name' => $marketData['name'],
                    'is_active' => true,
                ]);

                foreach ($marketData['odds'] as $oddData) {
                    $market->odds()->create([
                        'label' => $oddData['label'],
                        'value' => $oddData['value'],
                        'is_active' => true,
                    ]);
                }
            }
        });

        return response()->json([
            'message' => 'Odds updated successfully.',
            'match' => new MatchResource($match->load('markets.odds')),
        ]);
    }
}
