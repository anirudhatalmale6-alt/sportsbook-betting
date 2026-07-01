<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MatchResource;
use App\Models\GameMatch;
use App\Services\OddsApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class OddsController extends Controller
{
    protected OddsApiService $oddsApi;

    public function __construct(OddsApiService $oddsApi)
    {
        $this->oddsApi = $oddsApi;
    }

    /**
     * Get available sports from The Odds API.
     */
    public function getSports(): JsonResponse
    {
        $sports = $this->oddsApi->getSports();

        if ($sports === null) {
            return response()->json([
                'message' => 'Failed to fetch sports from API.',
            ], 503);
        }

        // Filter to only sports that map to our internal structure
        $mapped = collect($sports)->filter(fn ($s) => $s['internal_sport_slug'] !== null);

        return response()->json([
            'data' => $mapped->values(),
            'total' => $mapped->count(),
        ]);
    }

    /**
     * Manually trigger an odds sync (admin only).
     */
    public function refreshOdds(Request $request): JsonResponse
    {
        $sportKey = $request->input('sport_key');
        $markets = $request->input('markets', 'h2h');

        Log::info('OddsController: Manual odds refresh triggered', [
            'user_id' => $request->user()->id,
            'sport_key' => $sportKey,
        ]);

        $params = ['--markets' => $markets, '--force' => true];
        if ($sportKey) {
            $params['--sport'] = $sportKey;
        }

        Artisan::call('odds:sync', $params);
        $output = Artisan::output();

        return response()->json([
            'message' => 'Odds sync completed.',
            'output' => trim($output),
        ]);
    }

    /**
     * Get detailed odds for a specific event/match.
     */
    public function getEventOdds(int $eventId): JsonResponse
    {
        $match = GameMatch::with(['league.sport', 'league.country', 'markets.odds'])
            ->findOrFail($eventId);

        // If the match has an api_id, try to get fresh odds from the API
        $apiOdds = null;
        if ($match->api_id && $match->league && $match->league->api_key) {
            $events = $this->oddsApi->getOdds($match->league->api_key, 'h2h,spreads,totals');

            if ($events) {
                $apiOdds = collect($events)->firstWhere('api_id', $match->api_id);
            }
        }

        return response()->json([
            'match' => new MatchResource($match),
            'api_odds' => $apiOdds ? [
                'all_bookmakers' => $apiOdds['all_bookmakers'] ?? [],
                'markets' => $apiOdds['markets'] ?? [],
            ] : null,
        ]);
    }
}
