<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MatchResource;
use App\Models\GameMatch;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index(Request $request)
    {
        $query = GameMatch::with(['league.sport', 'league.country']);

        if ($request->has('sport_id')) {
            $query->whereHas('league', fn($q) => $q->where('sport_id', $request->sport_id));
        }

        if ($request->has('league_id')) {
            $query->where('league_id', $request->league_id);
        }

        if ($request->has('date')) {
            $query->whereDate('start_time', $request->date);
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $matches = $query->orderBy('start_time')->paginate($request->get('per_page', 20));

        return MatchResource::collection($matches);
    }

    public function show(int $id)
    {
        $match = GameMatch::with(['league.sport', 'league.country', 'markets.odds'])
            ->findOrFail($id);

        return new MatchResource($match);
    }

    public function live()
    {
        $matches = GameMatch::with(['league.sport', 'league.country', 'markets.odds'])
            ->where('status', 'live')
            ->orderBy('start_time')
            ->get();

        return MatchResource::collection($matches);
    }

    public function featured()
    {
        $matches = GameMatch::with(['league.sport', 'league.country', 'markets.odds'])
            ->where('is_featured', true)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get();

        return MatchResource::collection($matches);
    }
}
