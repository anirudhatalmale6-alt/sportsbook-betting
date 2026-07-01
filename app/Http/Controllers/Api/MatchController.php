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
        $query = GameMatch::with(['league.sport', 'league.country', 'markets.odds']);

        // Filter by sport
        if ($request->has('sport_id')) {
            $query->whereHas('league', fn($q) => $q->where('sport_id', $request->sport_id));
        }

        if ($request->has('sport_slug')) {
            $query->whereHas('league.sport', fn($q) => $q->where('slug', $request->sport_slug));
        }

        // Filter by league
        if ($request->has('league_id')) {
            $query->where('league_id', $request->league_id);
        }

        if ($request->has('league_slug')) {
            $query->whereHas('league', fn($q) => $q->where('slug', $request->league_slug));
        }

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('start_time', $request->date);
        }

        // Filter by featured
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Filter by status (supports single or comma-separated: "live,upcoming")
        if ($request->has('status')) {
            $statuses = explode(',', $request->status);
            $query->whereIn('status', $statuses);
        }

        // Search by team name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('home_team', 'like', "%{$search}%")
                  ->orWhere('away_team', 'like', "%{$search}%");
            });
        }

        // Exclude finished/cancelled by default unless status filter is provided
        if (!$request->has('status') && !$request->boolean('include_finished')) {
            $query->whereNotIn('status', ['finished', 'cancelled']);
        }

        // Sort: live matches first, then by start_time ascending
        $sortBy = $request->get('sort_by', 'start_time');
        $sortDir = $request->get('sort_dir', 'asc');

        $allowedSorts = ['start_time', 'created_at', 'home_team'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'start_time';
        }

        $query->orderByRaw("CASE WHEN status = 'live' THEN 0 WHEN status = 'upcoming' THEN 1 ELSE 2 END")
              ->orderBy($sortBy, $sortDir);

        $perPage = min((int) $request->get('per_page', 20), 100);
        $matches = $query->paginate($perPage);

        return MatchResource::collection($matches);
    }

    public function show(int $id)
    {
        $match = GameMatch::with(['league.sport', 'league.country', 'markets.odds'])
            ->findOrFail($id);

        return new MatchResource($match);
    }

    public function live(Request $request)
    {
        $query = GameMatch::with(['league.sport', 'league.country', 'markets.odds'])
            ->where('status', 'live')
            ->orderBy('start_time');

        if ($request->has('sport_id')) {
            $query->whereHas('league', fn($q) => $q->where('sport_id', $request->sport_id));
        }

        if ($request->has('league_id')) {
            $query->where('league_id', $request->league_id);
        }

        $perPage = min((int) $request->get('per_page', 20), 100);

        return MatchResource::collection($query->paginate($perPage));
    }

    public function upcoming(Request $request)
    {
        $query = GameMatch::with(['league.sport', 'league.country', 'markets.odds'])
            ->where('status', 'upcoming')
            ->where('start_time', '>=', now())
            ->orderBy('start_time');

        if ($request->has('sport_id')) {
            $query->whereHas('league', fn($q) => $q->where('sport_id', $request->sport_id));
        }

        if ($request->has('league_id')) {
            $query->where('league_id', $request->league_id);
        }

        // Optional: limit to next N hours
        if ($request->has('hours')) {
            $query->where('start_time', '<=', now()->addHours((int) $request->hours));
        }

        $perPage = min((int) $request->get('per_page', 20), 100);

        return MatchResource::collection($query->paginate($perPage));
    }

    public function featured(Request $request)
    {
        $query = GameMatch::with(['league.sport', 'league.country', 'markets.odds'])
            ->where('is_featured', true)
            ->where('status', '!=', 'cancelled')
            ->orderByRaw("CASE WHEN status = 'live' THEN 0 WHEN status = 'upcoming' THEN 1 ELSE 2 END")
            ->orderBy('start_time');

        $perPage = min((int) $request->get('per_page', 20), 100);

        return MatchResource::collection($query->paginate($perPage));
    }
}
