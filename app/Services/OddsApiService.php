<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OddsApiService
{
    protected string $baseUrl;
    protected string $apiKey;

    /**
     * Mapping of The Odds API sport keys to our internal sport slugs.
     */
    protected array $sportKeyMap = [
        'soccer'            => 'football',
        'basketball'        => 'basketball',
        'americanfootball'  => 'american-football',
        'baseball'          => 'baseball',
        'mma'               => 'combat-sports',
        'tennis'            => 'tennis',
        'handball'          => 'handball',
        'volleyball'        => 'volleyball',
        'tabletennis'       => 'table-tennis',
        'icehockey'         => 'ice-hockey',
        'cricket'           => 'cricket',
        'rugbyleague'       => 'rugby',
        'rugbyunion'        => 'rugby',
        'golf'              => 'golf',
    ];

    /**
     * Mapping of The Odds API sport keys to our internal league slugs.
     */
    protected array $leagueKeyMap = [
        // Football / Soccer
        'soccer_epl'                    => 'premier-league',
        'soccer_spain_la_liga'          => 'la-liga',
        'soccer_germany_bundesliga'     => 'bundesliga',
        'soccer_italy_serie_a'         => 'serie-a',
        'soccer_france_ligue_one'       => 'ligue-1',
        'soccer_turkey_super_league'    => 'super-lig',
        'soccer_uefa_champs_league'     => 'champions-league',
        'soccer_brazil_campeonato'      => 'serie-a-brazil',
        'soccer_usa_mls'                => 'mls',
        'soccer_australia_aleague'      => 'a-league',
        'soccer_efl_champ'             => 'efl-championship',
        'soccer_fa_cup'                => 'fa-cup',
        'soccer_uefa_europa_league'    => 'europa-league',
        'soccer_conmebol_copa_libertadores' => 'copa-libertadores',
        'soccer_mexico_ligamx'         => 'liga-mx',

        // Basketball
        'basketball_nba'               => 'nba',
        'basketball_euroleague'        => 'euroleague',
        'basketball_nbl'               => 'nbl',
        'basketball_ncaab'             => 'ncaab',

        // American Football
        'americanfootball_nfl'          => 'nfl',
        'americanfootball_ncaaf'        => 'ncaaf',

        // Baseball
        'baseball_mlb'                 => 'mlb',

        // Combat Sports / MMA
        'mma_mixed_martial_arts'        => 'ufc',

        // Tennis
        'tennis_atp_french_open'        => 'atp-tour',
        'tennis_atp_wimbledon'          => 'atp-tour',
        'tennis_atp_us_open'            => 'atp-tour',
        'tennis_atp_australian_open'    => 'atp-tour',
        'tennis_wta_french_open'        => 'wta-tour',
        'tennis_wta_wimbledon'          => 'wta-tour',
        'tennis_wta_us_open'            => 'wta-tour',
        'tennis_wta_australian_open'    => 'wta-tour',

        // Handball
        'handball_european_championship'  => 'ehf-champions-league',
        'handball_germany_bundesliga'     => 'handball-bundesliga',

        // Horse Racing
        'horse_racing_uk'              => 'uk-horse-racing',
        'horse_racing_us'              => 'us-horse-racing',
    ];

    public function __construct()
    {
        $this->baseUrl = config('services.odds_api.base_url', 'https://api.the-odds-api.com/v4');
        $this->apiKey = config('services.odds_api.key', '');
    }

    /**
     * Get list of available sports from the API.
     *
     * @return array|null
     */
    public function getSports(): ?array
    {
        return Cache::remember('odds_api:sports', 3600, function () {
            try {
                $response = Http::timeout(15)->get("{$this->baseUrl}/sports", [
                    'apiKey' => $this->apiKey,
                ]);

                if ($response->failed()) {
                    Log::error('OddsAPI: Failed to fetch sports', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    return null;
                }

                $this->logRemainingRequests($response);

                return collect($response->json())->map(function ($sport) {
                    return [
                        'key'          => $sport['key'],
                        'group'        => $sport['group'],
                        'title'        => $sport['title'],
                        'description'  => $sport['description'] ?? null,
                        'active'       => $sport['active'] ?? true,
                        'has_outrights' => $sport['has_outrights'] ?? false,
                        'internal_sport_slug' => $this->mapSportKey($sport['key']),
                        'internal_league_slug' => $this->mapLeagueKey($sport['key']),
                    ];
                })->toArray();
            } catch (\Exception $e) {
                Log::error('OddsAPI: Exception fetching sports', ['error' => $e->getMessage()]);
                return null;
            }
        });
    }

    /**
     * Get upcoming events for a specific sport.
     *
     * @param string $sportKey The Odds API sport key (e.g., 'soccer_epl')
     * @return array|null
     */
    public function getEvents(string $sportKey): ?array
    {
        $cacheKey = "odds_api:events:{$sportKey}";

        return Cache::remember($cacheKey, 300, function () use ($sportKey) {
            try {
                $response = Http::timeout(15)->get("{$this->baseUrl}/sports/{$sportKey}/odds", [
                    'apiKey'   => $this->apiKey,
                    'regions'  => 'eu',
                    'markets'  => 'h2h',
                    'oddsFormat' => 'decimal',
                ]);

                if ($response->failed()) {
                    Log::error('OddsAPI: Failed to fetch events', [
                        'sport' => $sportKey,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    return null;
                }

                $this->logRemainingRequests($response);

                return collect($response->json())->map(function ($event) use ($sportKey) {
                    return $this->normalizeEvent($event, $sportKey);
                })->toArray();
            } catch (\Exception $e) {
                Log::error('OddsAPI: Exception fetching events', [
                    'sport' => $sportKey,
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    /**
     * Get detailed odds for a sport with specific markets.
     *
     * @param string $sportKey  The Odds API sport key
     * @param string $markets   Comma-separated markets (e.g., 'h2h,spreads,totals')
     * @return array|null
     */
    public function getOdds(string $sportKey, string $markets = 'h2h'): ?array
    {
        $cacheKey = "odds_api:odds:{$sportKey}:{$markets}";

        return Cache::remember($cacheKey, 300, function () use ($sportKey, $markets) {
            try {
                $response = Http::timeout(15)->get("{$this->baseUrl}/sports/{$sportKey}/odds", [
                    'apiKey'     => $this->apiKey,
                    'regions'    => 'eu',
                    'markets'    => $markets,
                    'oddsFormat' => 'decimal',
                ]);

                if ($response->failed()) {
                    Log::error('OddsAPI: Failed to fetch odds', [
                        'sport' => $sportKey,
                        'markets' => $markets,
                        'status' => $response->status(),
                    ]);
                    return null;
                }

                $this->logRemainingRequests($response);

                return collect($response->json())->map(function ($event) use ($sportKey) {
                    return $this->normalizeEvent($event, $sportKey);
                })->toArray();
            } catch (\Exception $e) {
                Log::error('OddsAPI: Exception fetching odds', [
                    'sport' => $sportKey,
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    /**
     * Get live scores for a sport.
     *
     * @param string $sportKey The Odds API sport key
     * @return array|null
     */
    public function getLiveScores(string $sportKey): ?array
    {
        // No caching for live scores -- they need to be fresh
        try {
            $response = Http::timeout(15)->get("{$this->baseUrl}/sports/{$sportKey}/scores", [
                'apiKey'     => $this->apiKey,
                'daysFrom'   => 1,
            ]);

            if ($response->failed()) {
                Log::error('OddsAPI: Failed to fetch live scores', [
                    'sport' => $sportKey,
                    'status' => $response->status(),
                ]);
                return null;
            }

            $this->logRemainingRequests($response);

            return collect($response->json())->map(function ($event) use ($sportKey) {
                $scores = collect($event['scores'] ?? []);

                return [
                    'api_id'       => $event['id'],
                    'sport_key'    => $event['sport_key'],
                    'home_team'    => $event['home_team'],
                    'away_team'    => $event['away_team'],
                    'commence_time' => $event['commence_time'],
                    'completed'    => $event['completed'] ?? false,
                    'score_home'   => $scores->firstWhere('name', $event['home_team'])['score'] ?? null,
                    'score_away'   => $scores->firstWhere('name', $event['away_team'])['score'] ?? null,
                    'last_update'  => $event['last_update'] ?? null,
                    'internal_sport_slug' => $this->mapSportKey($sportKey),
                    'internal_league_slug' => $this->mapLeagueKey($sportKey),
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('OddsAPI: Exception fetching live scores', [
                'sport' => $sportKey,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Normalize an event from the API into a clean structure.
     */
    protected function normalizeEvent(array $event, string $sportKey): array
    {
        $bookmakers = collect($event['bookmakers'] ?? []);

        // Pick the best bookmaker: prefer pinnacle, then any available
        $primaryBookmaker = $bookmakers->firstWhere('key', 'pinnacle')
            ?? $bookmakers->firstWhere('key', 'betfair_ex_eu')
            ?? $bookmakers->first();

        $markets = [];

        if ($primaryBookmaker) {
            foreach ($primaryBookmaker['markets'] ?? [] as $market) {
                $outcomes = collect($market['outcomes'] ?? [])->map(function ($outcome) {
                    return [
                        'label' => $outcome['name'],
                        'value' => round((float) $outcome['price'], 2),
                        'point' => $outcome['point'] ?? null,
                    ];
                })->toArray();

                $markets[] = [
                    'key'       => $market['key'],
                    'name'      => $this->formatMarketName($market['key']),
                    'bookmaker' => $primaryBookmaker['title'],
                    'outcomes'  => $outcomes,
                    'last_update' => $primaryBookmaker['last_update'] ?? null,
                ];
            }
        }

        // Also collect all bookmaker odds for the h2h market (for comparison)
        $allBookmakerOdds = $bookmakers->map(function ($bookmaker) {
            $h2h = collect($bookmaker['markets'] ?? [])->firstWhere('key', 'h2h');
            if (!$h2h) {
                return null;
            }
            return [
                'bookmaker' => $bookmaker['title'],
                'bookmaker_key' => $bookmaker['key'],
                'outcomes' => collect($h2h['outcomes'])->map(fn ($o) => [
                    'label' => $o['name'],
                    'value' => round((float) $o['price'], 2),
                ])->toArray(),
                'last_update' => $bookmaker['last_update'] ?? null,
            ];
        })->filter()->values()->toArray();

        return [
            'api_id'         => $event['id'],
            'sport_key'      => $event['sport_key'],
            'sport_title'    => $event['sport_title'] ?? null,
            'home_team'      => $event['home_team'],
            'away_team'      => $event['away_team'],
            'commence_time'  => $event['commence_time'],
            'markets'        => $markets,
            'all_bookmakers'  => $allBookmakerOdds,
            'internal_sport_slug' => $this->mapSportKey($sportKey),
            'internal_league_slug' => $this->mapLeagueKey($sportKey),
        ];
    }

    /**
     * Map an API sport key to our internal sport slug.
     * E.g., "soccer_epl" -> "football"
     */
    public function mapSportKey(string $apiKey): ?string
    {
        $prefix = explode('_', $apiKey)[0];
        return $this->sportKeyMap[$prefix] ?? null;
    }

    /**
     * Map an API sport key to our internal league slug.
     * E.g., "soccer_epl" -> "premier-league"
     */
    public function mapLeagueKey(string $apiKey): ?string
    {
        return $this->leagueKeyMap[$apiKey] ?? null;
    }

    /**
     * Get human-readable market name from API market key.
     */
    protected function formatMarketName(string $key): string
    {
        return match ($key) {
            'h2h'       => 'Match Winner',
            'spreads'   => 'Spread',
            'totals'    => 'Over/Under',
            'outrights' => 'Outright Winner',
            'h2h_lay'   => 'Match Winner (Lay)',
            default     => str_replace('_', ' ', ucfirst($key)),
        };
    }

    /**
     * Get all supported API sport keys that map to our leagues.
     */
    public function getSupportedLeagueKeys(): array
    {
        return array_keys($this->leagueKeyMap);
    }

    /**
     * Log remaining API request quota from response headers.
     */
    protected function logRemainingRequests($response): void
    {
        $remaining = $response->header('x-requests-remaining');
        $used = $response->header('x-requests-used');

        if ($remaining !== null) {
            Log::info('OddsAPI: Request quota', [
                'remaining' => $remaining,
                'used' => $used,
            ]);

            if ((int) $remaining < 50) {
                Log::warning('OddsAPI: Low request quota remaining', [
                    'remaining' => $remaining,
                ]);
            }
        }
    }

    /**
     * Clear all cached API data.
     */
    public function clearCache(): void
    {
        Cache::forget('odds_api:sports');

        foreach ($this->getSupportedLeagueKeys() as $key) {
            Cache::forget("odds_api:events:{$key}");
            Cache::forget("odds_api:odds:{$key}:h2h");
            Cache::forget("odds_api:odds:{$key}:h2h,spreads,totals");
        }
    }
}
