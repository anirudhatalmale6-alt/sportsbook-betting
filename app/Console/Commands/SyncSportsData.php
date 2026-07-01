<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\GameMatch;
use App\Models\League;
use App\Models\Market;
use App\Models\Odd;
use App\Models\Sport;
use App\Services\OddsApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncSportsData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'odds:sync
        {--sport= : Sync a specific API sport key (e.g., soccer_epl)}
        {--markets=h2h : Comma-separated markets to fetch (h2h,spreads,totals)}
        {--force : Force refresh ignoring cache}';

    /**
     * The console command description.
     */
    protected $description = 'Sync sports, events, and odds from The Odds API';

    protected OddsApiService $oddsApi;

    protected int $synced = 0;
    protected int $created = 0;
    protected int $updated = 0;
    protected int $errors = 0;

    public function handle(OddsApiService $oddsApi): int
    {
        $this->oddsApi = $oddsApi;

        if ($this->option('force')) {
            $oddsApi->clearCache();
            $this->info('Cache cleared.');
        }

        $startTime = now();
        $this->info('Starting odds sync at ' . $startTime->toDateTimeString());
        Log::info('OddsSync: Starting sync');

        // Step 1: Determine which sport keys to sync
        $sportKeys = $this->getSportKeysToSync();

        if (empty($sportKeys)) {
            $this->warn('No sport keys to sync.');
            return self::SUCCESS;
        }

        $this->info('Syncing ' . count($sportKeys) . ' sport/league keys...');
        $bar = $this->output->createProgressBar(count($sportKeys));
        $bar->start();

        // Step 2: Fetch and sync events + odds for each sport key
        foreach ($sportKeys as $apiKey) {
            try {
                $this->syncSportKey($apiKey);
            } catch (\Exception $e) {
                $this->errors++;
                Log::error('OddsSync: Error syncing sport key', [
                    'key' => $apiKey,
                    'error' => $e->getMessage(),
                ]);
                $this->error("\nError syncing {$apiKey}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $duration = $startTime->diffInSeconds(now());
        $summary = "Sync complete in {$duration}s: {$this->created} created, {$this->updated} updated, {$this->errors} errors";
        $this->info($summary);
        Log::info("OddsSync: {$summary}");

        return self::SUCCESS;
    }

    /**
     * Determine which API sport keys to sync.
     */
    protected function getSportKeysToSync(): array
    {
        if ($specificKey = $this->option('sport')) {
            return [$specificKey];
        }

        // First try to get the live list from the API
        $apiSports = $this->oddsApi->getSports();

        if ($apiSports) {
            // Filter to only sports that map to our internal leagues
            return collect($apiSports)
                ->filter(fn ($s) => $s['active'] && !$s['has_outrights'])
                ->pluck('key')
                ->filter(fn ($key) => $this->oddsApi->mapLeagueKey($key) !== null)
                ->values()
                ->toArray();
        }

        // Fallback: use the hardcoded supported keys
        return $this->oddsApi->getSupportedLeagueKeys();
    }

    /**
     * Sync events and odds for a single API sport key.
     */
    protected function syncSportKey(string $apiKey): void
    {
        $markets = $this->option('markets');
        $events = $this->oddsApi->getOdds($apiKey, $markets);

        if ($events === null) {
            Log::warning("OddsSync: No events returned for {$apiKey}");
            return;
        }

        if (empty($events)) {
            return;
        }

        foreach ($events as $event) {
            $this->syncEvent($event, $apiKey);
        }
    }

    /**
     * Sync a single event (match + markets + odds).
     */
    protected function syncEvent(array $event, string $apiKey): void
    {
        DB::beginTransaction();

        try {
            // Resolve the league
            $league = $this->resolveLeague($event, $apiKey);
            if (!$league) {
                DB::rollBack();
                return;
            }

            // Determine match status based on commence time
            $commenceTime = Carbon::parse($event['commence_time']);
            $status = $commenceTime->isPast() ? 'live' : 'upcoming';

            // Create or update the match
            $match = GameMatch::updateOrCreate(
                ['api_id' => $event['api_id']],
                [
                    'league_id'  => $league->id,
                    'home_team'  => $event['home_team'],
                    'away_team'  => $event['away_team'],
                    'start_time' => $commenceTime,
                    'status'     => $status,
                ]
            );

            if ($match->wasRecentlyCreated) {
                $this->created++;
            } else {
                $this->updated++;
            }

            // Sync markets and odds
            $this->syncMarkets($match, $event['markets'] ?? []);

            DB::commit();
            $this->synced++;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors++;
            Log::error('OddsSync: Error syncing event', [
                'api_id' => $event['api_id'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Resolve or create the league for an event.
     */
    protected function resolveLeague(array $event, string $apiKey): ?League
    {
        $leagueSlug = $event['internal_league_slug'] ?? $this->oddsApi->mapLeagueKey($apiKey);

        if ($leagueSlug) {
            // Try to find existing league by slug
            $league = League::where('slug', $leagueSlug)->first();
            if ($league) {
                // Update api_key if not set
                if (!$league->api_key) {
                    $league->update(['api_key' => $apiKey]);
                }
                return $league;
            }
        }

        // Try to find by api_key
        $league = League::where('api_key', $apiKey)->first();
        if ($league) {
            return $league;
        }

        // Auto-create league from API data
        $sportSlug = $event['internal_sport_slug'] ?? $this->oddsApi->mapSportKey($apiKey);
        $sport = Sport::where('slug', $sportSlug)->first();

        if (!$sport) {
            Log::warning("OddsSync: No matching sport for key {$apiKey}");
            return null;
        }

        // Use International as default country for auto-created leagues
        $country = Country::where('code', 'INT')->first();
        if (!$country) {
            $country = Country::firstOrCreate(
                ['code' => 'INT'],
                ['name' => 'International']
            );
        }

        $leagueName = $event['sport_title'] ?? Str::title(str_replace('_', ' ', $apiKey));
        $leagueSlug = $leagueSlug ?? Str::slug($leagueName);

        // Ensure unique slug
        $baseSlug = $leagueSlug;
        $counter = 1;
        while (League::where('slug', $leagueSlug)->exists()) {
            $leagueSlug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $league = League::create([
            'name'       => $leagueName,
            'slug'       => $leagueSlug,
            'api_key'    => $apiKey,
            'sport_id'   => $sport->id,
            'country_id' => $country->id,
            'is_active'  => true,
            'order'      => 99,
        ]);

        Log::info("OddsSync: Auto-created league", [
            'name' => $leagueName,
            'slug' => $leagueSlug,
            'api_key' => $apiKey,
        ]);

        return $league;
    }

    /**
     * Sync markets and odds for a match.
     */
    protected function syncMarkets(GameMatch $match, array $apiMarkets): void
    {
        foreach ($apiMarkets as $apiMarket) {
            $marketName = $apiMarket['name'];
            $bookmaker = $apiMarket['bookmaker'] ?? null;

            // Find or create market for this match
            $market = Market::updateOrCreate(
                [
                    'match_id'  => $match->id,
                    'name'      => $marketName,
                    'bookmaker' => $bookmaker,
                ],
                [
                    'is_active' => true,
                ]
            );

            // Sync odds for this market
            $this->syncOdds($market, $apiMarket['outcomes'] ?? []);
        }
    }

    /**
     * Sync odds for a market.
     */
    protected function syncOdds(Market $market, array $outcomes): void
    {
        // Deactivate old odds that may no longer be present
        $market->odds()->update(['is_active' => false]);

        foreach ($outcomes as $outcome) {
            Odd::updateOrCreate(
                [
                    'market_id' => $market->id,
                    'label'     => $outcome['label'],
                ],
                [
                    'value'     => $outcome['value'],
                    'is_active' => true,
                ]
            );
        }
    }
}
