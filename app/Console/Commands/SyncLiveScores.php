<?php

namespace App\Console\Commands;

use App\Models\GameMatch;
use App\Models\League;
use App\Services\OddsApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncLiveScores extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'odds:live-scores
        {--sport= : Fetch scores for a specific API sport key only}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch and update live scores from The Odds API';

    protected int $updatedCount = 0;
    protected int $finishedCount = 0;

    public function handle(OddsApiService $oddsApi): int
    {
        $startTime = now();
        $this->info('Fetching live scores at ' . $startTime->toDateTimeString());
        Log::info('LiveScores: Starting score sync');

        // Get sport keys that have live or recent matches
        $sportKeys = $this->getActiveSportKeys($oddsApi);

        if (empty($sportKeys)) {
            $this->info('No active sport keys with live matches.');
            return self::SUCCESS;
        }

        $this->info('Checking scores for ' . count($sportKeys) . ' sport keys...');

        foreach ($sportKeys as $apiKey) {
            try {
                $this->syncScoresForSport($oddsApi, $apiKey);
            } catch (\Exception $e) {
                Log::error('LiveScores: Error fetching scores', [
                    'sport' => $apiKey,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Error fetching scores for {$apiKey}: {$e->getMessage()}");
            }
        }

        $duration = $startTime->diffInSeconds(now());
        $summary = "Score sync complete in {$duration}s: {$this->updatedCount} updated, {$this->finishedCount} finished";
        $this->info($summary);
        Log::info("LiveScores: {$summary}");

        return self::SUCCESS;
    }

    /**
     * Get API sport keys that currently have live or recently started matches.
     */
    protected function getActiveSportKeys(OddsApiService $oddsApi): array
    {
        if ($specific = $this->option('sport')) {
            return [$specific];
        }

        // Find leagues that have live matches or matches that started in the last 4 hours
        $cutoff = now()->subHours(4);

        $activeLeagues = League::whereNotNull('api_key')
            ->whereHas('matches', function ($q) use ($cutoff) {
                $q->where(function ($sub) use ($cutoff) {
                    $sub->where('status', 'live')
                        ->orWhere(function ($inner) use ($cutoff) {
                            $inner->where('status', 'upcoming')
                                ->where('start_time', '<=', now());
                        })
                        ->orWhere(function ($inner) use ($cutoff) {
                            $inner->where('start_time', '>=', $cutoff)
                                ->where('status', '!=', 'cancelled');
                        });
                });
            })
            ->pluck('api_key')
            ->unique()
            ->values()
            ->toArray();

        return $activeLeagues;
    }

    /**
     * Sync live scores for a specific API sport key.
     */
    protected function syncScoresForSport(OddsApiService $oddsApi, string $apiKey): void
    {
        $scores = $oddsApi->getLiveScores($apiKey);

        if ($scores === null) {
            return;
        }

        foreach ($scores as $scoreData) {
            $this->updateMatchScore($scoreData);
        }
    }

    /**
     * Update a single match with score data.
     */
    protected function updateMatchScore(array $scoreData): void
    {
        $match = GameMatch::where('api_id', $scoreData['api_id'])->first();

        if (!$match) {
            return;
        }

        DB::beginTransaction();

        try {
            $updates = [];

            // Update scores if available
            if ($scoreData['score_home'] !== null) {
                $updates['score_home'] = (int) $scoreData['score_home'];
            }
            if ($scoreData['score_away'] !== null) {
                $updates['score_away'] = (int) $scoreData['score_away'];
            }

            // Update status
            if ($scoreData['completed']) {
                $updates['status'] = 'finished';
                $this->finishedCount++;

                // Deactivate all markets for finished matches
                $match->markets()->update(['is_active' => false]);

                Log::info('LiveScores: Match finished', [
                    'match_id' => $match->id,
                    'home' => $match->home_team,
                    'away' => $match->away_team,
                    'score' => ($updates['score_home'] ?? '?') . ' - ' . ($updates['score_away'] ?? '?'),
                ]);
            } else {
                // If commence time has passed, mark as live
                if ($match->start_time->isPast() && $match->status === 'upcoming') {
                    $updates['status'] = 'live';
                }
            }

            if (!empty($updates)) {
                $match->update($updates);
                $this->updatedCount++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LiveScores: Error updating match score', [
                'match_id' => $match->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
