<?php

namespace Database\Seeders;

use App\Models\GameMatch;
use App\Models\League;
use App\Models\Market;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MatchSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Premier League matches
        $pl = League::where('slug', 'premier-league')->first();
        $this->createMatch($pl, 'Manchester United', 'Liverpool', $now->copy()->addHours(2), 'upcoming', true, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 2.90], ['label' => 'X', 'value' => 3.40], ['label' => '2', 'value' => 2.45]]],
            ['name' => 'Over/Under 2.5', 'odds' => [['label' => 'Over 2.5', 'value' => 1.85], ['label' => 'Under 2.5', 'value' => 1.95]]],
            ['name' => 'Both Teams to Score', 'odds' => [['label' => 'Yes', 'value' => 1.70], ['label' => 'No', 'value' => 2.10]]],
        ]);

        $this->createMatch($pl, 'Arsenal', 'Chelsea', $now->copy()->addHours(5), 'upcoming', true, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 1.95], ['label' => 'X', 'value' => 3.60], ['label' => '2', 'value' => 3.80]]],
            ['name' => 'Over/Under 2.5', 'odds' => [['label' => 'Over 2.5', 'value' => 1.90], ['label' => 'Under 2.5', 'value' => 1.90]]],
            ['name' => 'Both Teams to Score', 'odds' => [['label' => 'Yes', 'value' => 1.75], ['label' => 'No', 'value' => 2.05]]],
        ]);

        $this->createMatch($pl, 'Manchester City', 'Tottenham', $now->copy()->addHours(26), 'upcoming', false, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 1.45], ['label' => 'X', 'value' => 4.50], ['label' => '2', 'value' => 6.50]]],
            ['name' => 'Over/Under 2.5', 'odds' => [['label' => 'Over 2.5', 'value' => 1.65], ['label' => 'Under 2.5', 'value' => 2.20]]],
        ]);

        $this->createMatch($pl, 'Newcastle', 'Aston Villa', $now->copy()->addHours(28), 'upcoming', false, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 2.10], ['label' => 'X', 'value' => 3.40], ['label' => '2', 'value' => 3.30]]],
            ['name' => 'Over/Under 2.5', 'odds' => [['label' => 'Over 2.5', 'value' => 1.80], ['label' => 'Under 2.5', 'value' => 2.00]]],
        ]);

        // Live match
        $this->createMatch($pl, 'West Ham', 'Brighton', $now->copy()->subMinutes(35), 'live', false, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 2.50], ['label' => 'X', 'value' => 3.20], ['label' => '2', 'value' => 2.80]]],
            ['name' => 'Over/Under 2.5', 'odds' => [['label' => 'Over 2.5', 'value' => 1.75], ['label' => 'Under 2.5', 'value' => 2.05]]],
        ], 1, 0);

        // La Liga matches
        $laliga = League::where('slug', 'la-liga')->first();
        $this->createMatch($laliga, 'Real Madrid', 'Barcelona', $now->copy()->addHours(3), 'upcoming', true, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 2.20], ['label' => 'X', 'value' => 3.50], ['label' => '2', 'value' => 3.10]]],
            ['name' => 'Over/Under 2.5', 'odds' => [['label' => 'Over 2.5', 'value' => 1.70], ['label' => 'Under 2.5', 'value' => 2.10]]],
            ['name' => 'Both Teams to Score', 'odds' => [['label' => 'Yes', 'value' => 1.60], ['label' => 'No', 'value' => 2.25]]],
        ]);

        $this->createMatch($laliga, 'Atletico Madrid', 'Sevilla', $now->copy()->addHours(8), 'upcoming', false, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 1.60], ['label' => 'X', 'value' => 3.80], ['label' => '2', 'value' => 5.50]]],
            ['name' => 'Over/Under 2.5', 'odds' => [['label' => 'Over 2.5', 'value' => 1.95], ['label' => 'Under 2.5', 'value' => 1.85]]],
        ]);

        $this->createMatch($laliga, 'Valencia', 'Real Sociedad', $now->copy()->subMinutes(60), 'live', false, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 3.10], ['label' => 'X', 'value' => 2.80], ['label' => '2', 'value' => 2.40]]],
        ], 0, 1);

        // Bundesliga
        $bundesliga = League::where('slug', 'bundesliga')->first();
        $this->createMatch($bundesliga, 'Bayern Munich', 'Borussia Dortmund', $now->copy()->addHours(4), 'upcoming', true, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 1.55], ['label' => 'X', 'value' => 4.20], ['label' => '2', 'value' => 5.00]]],
            ['name' => 'Over/Under 2.5', 'odds' => [['label' => 'Over 2.5', 'value' => 1.50], ['label' => 'Under 2.5', 'value' => 2.50]]],
            ['name' => 'Both Teams to Score', 'odds' => [['label' => 'Yes', 'value' => 1.55], ['label' => 'No', 'value' => 2.35]]],
        ]);

        $this->createMatch($bundesliga, 'RB Leipzig', 'Bayer Leverkusen', $now->copy()->addHours(24), 'upcoming', false, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 2.60], ['label' => 'X', 'value' => 3.40], ['label' => '2', 'value' => 2.70]]],
            ['name' => 'Over/Under 2.5', 'odds' => [['label' => 'Over 2.5', 'value' => 1.75], ['label' => 'Under 2.5', 'value' => 2.05]]],
        ]);

        // Serie A
        $serieA = League::where('slug', 'serie-a')->first();
        $this->createMatch($serieA, 'AC Milan', 'Inter Milan', $now->copy()->addHours(6), 'upcoming', true, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 2.80], ['label' => 'X', 'value' => 3.30], ['label' => '2', 'value' => 2.50]]],
            ['name' => 'Over/Under 2.5', 'odds' => [['label' => 'Over 2.5', 'value' => 1.85], ['label' => 'Under 2.5', 'value' => 1.95]]],
        ]);

        $this->createMatch($serieA, 'Juventus', 'Napoli', $now->copy()->addHours(30), 'upcoming', false, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 2.15], ['label' => 'X', 'value' => 3.25], ['label' => '2', 'value' => 3.40]]],
            ['name' => 'Over/Under 2.5', 'odds' => [['label' => 'Over 2.5', 'value' => 2.00], ['label' => 'Under 2.5', 'value' => 1.80]]],
        ]);

        // Ligue 1
        $ligue1 = League::where('slug', 'ligue-1')->first();
        $this->createMatch($ligue1, 'PSG', 'Olympique Marseille', $now->copy()->addHours(7), 'upcoming', false, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 1.35], ['label' => 'X', 'value' => 5.00], ['label' => '2', 'value' => 8.00]]],
            ['name' => 'Over/Under 2.5', 'odds' => [['label' => 'Over 2.5', 'value' => 1.60], ['label' => 'Under 2.5', 'value' => 2.30]]],
        ]);

        // Champions League
        $cl = League::where('slug', 'champions-league')->first();
        $this->createMatch($cl, 'Real Madrid', 'Bayern Munich', $now->copy()->addHours(48), 'upcoming', true, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 2.30], ['label' => 'X', 'value' => 3.40], ['label' => '2', 'value' => 3.00]]],
            ['name' => 'Over/Under 2.5', 'odds' => [['label' => 'Over 2.5', 'value' => 1.65], ['label' => 'Under 2.5', 'value' => 2.20]]],
            ['name' => 'Both Teams to Score', 'odds' => [['label' => 'Yes', 'value' => 1.55], ['label' => 'No', 'value' => 2.40]]],
        ]);

        // NBA
        $nba = League::where('slug', 'nba')->first();
        $this->createMatch($nba, 'LA Lakers', 'Boston Celtics', $now->copy()->addHours(10), 'upcoming', true, [
            ['name' => 'Moneyline', 'odds' => [['label' => 'LA Lakers', 'value' => 2.10], ['label' => 'Boston Celtics', 'value' => 1.75]]],
            ['name' => 'Spread -5.5', 'odds' => [['label' => 'Lakers +5.5', 'value' => 1.90], ['label' => 'Celtics -5.5', 'value' => 1.90]]],
            ['name' => 'Total Points 215.5', 'odds' => [['label' => 'Over 215.5', 'value' => 1.90], ['label' => 'Under 215.5', 'value' => 1.90]]],
        ]);

        $this->createMatch($nba, 'Golden State Warriors', 'Miami Heat', $now->copy()->addHours(12), 'upcoming', false, [
            ['name' => 'Moneyline', 'odds' => [['label' => 'Warriors', 'value' => 1.65], ['label' => 'Heat', 'value' => 2.25]]],
            ['name' => 'Spread -4.5', 'odds' => [['label' => 'Warriors -4.5', 'value' => 1.90], ['label' => 'Heat +4.5', 'value' => 1.90]]],
            ['name' => 'Total Points 220.5', 'odds' => [['label' => 'Over 220.5', 'value' => 1.85], ['label' => 'Under 220.5', 'value' => 1.95]]],
        ]);

        // NBA live match
        $this->createMatch($nba, 'Milwaukee Bucks', 'Denver Nuggets', $now->copy()->subMinutes(45), 'live', false, [
            ['name' => 'Moneyline', 'odds' => [['label' => 'Bucks', 'value' => 1.80], ['label' => 'Nuggets', 'value' => 2.00]]],
        ], 58, 52);

        // UFC
        $ufc = League::where('slug', 'ufc')->first();
        $this->createMatch($ufc, 'Jon Jones', 'Stipe Miocic', $now->copy()->addHours(72), 'upcoming', true, [
            ['name' => 'Winner', 'odds' => [['label' => 'Jon Jones', 'value' => 1.40], ['label' => 'Stipe Miocic', 'value' => 3.00]]],
            ['name' => 'Method of Victory', 'odds' => [['label' => 'KO/TKO', 'value' => 2.20], ['label' => 'Submission', 'value' => 4.50], ['label' => 'Decision', 'value' => 2.60]]],
        ]);

        // NFL
        $nfl = League::where('slug', 'nfl')->first();
        $this->createMatch($nfl, 'Kansas City Chiefs', 'Philadelphia Eagles', $now->copy()->addHours(50), 'upcoming', true, [
            ['name' => 'Moneyline', 'odds' => [['label' => 'Chiefs', 'value' => 1.85], ['label' => 'Eagles', 'value' => 1.95]]],
            ['name' => 'Spread -2.5', 'odds' => [['label' => 'Chiefs -2.5', 'value' => 1.90], ['label' => 'Eagles +2.5', 'value' => 1.90]]],
            ['name' => 'Total Points 49.5', 'odds' => [['label' => 'Over 49.5', 'value' => 1.90], ['label' => 'Under 49.5', 'value' => 1.90]]],
        ]);

        // MLB
        $mlb = League::where('slug', 'mlb')->first();
        $this->createMatch($mlb, 'New York Yankees', 'Boston Red Sox', $now->copy()->addHours(8), 'upcoming', false, [
            ['name' => 'Moneyline', 'odds' => [['label' => 'Yankees', 'value' => 1.70], ['label' => 'Red Sox', 'value' => 2.15]]],
            ['name' => 'Run Line 1.5', 'odds' => [['label' => 'Yankees -1.5', 'value' => 2.20], ['label' => 'Red Sox +1.5', 'value' => 1.65]]],
            ['name' => 'Total Runs 8.5', 'odds' => [['label' => 'Over 8.5', 'value' => 1.85], ['label' => 'Under 8.5', 'value' => 1.95]]],
        ]);

        // Tennis - ATP
        $atp = League::where('slug', 'atp-tour')->first();
        $this->createMatch($atp, 'Carlos Alcaraz', 'Jannik Sinner', $now->copy()->addHours(20), 'upcoming', true, [
            ['name' => 'Winner', 'odds' => [['label' => 'Carlos Alcaraz', 'value' => 1.75], ['label' => 'Jannik Sinner', 'value' => 2.05]]],
            ['name' => 'Total Sets', 'odds' => [['label' => 'Over 3.5 Sets', 'value' => 2.10], ['label' => 'Under 3.5 Sets', 'value' => 1.70]]],
        ]);

        $this->createMatch($atp, 'Novak Djokovic', 'Rafael Nadal', $now->copy()->addHours(22), 'upcoming', false, [
            ['name' => 'Winner', 'odds' => [['label' => 'Djokovic', 'value' => 1.55], ['label' => 'Nadal', 'value' => 2.40]]],
        ]);

        // F1
        $f1League = League::where('slug', 'f1-world-championship')->first();
        $this->createMatch($f1League, 'British Grand Prix', 'Silverstone', $now->copy()->addHours(96), 'upcoming', true, [
            ['name' => 'Race Winner', 'odds' => [
                ['label' => 'Max Verstappen', 'value' => 1.80],
                ['label' => 'Lewis Hamilton', 'value' => 4.50],
                ['label' => 'Lando Norris', 'value' => 3.50],
                ['label' => 'Charles Leclerc', 'value' => 6.00],
                ['label' => 'Carlos Sainz', 'value' => 8.00],
                ['label' => 'Oscar Piastri', 'value' => 7.00],
            ]],
            ['name' => 'Podium Finish', 'odds' => [
                ['label' => 'Verstappen Podium', 'value' => 1.20],
                ['label' => 'Hamilton Podium', 'value' => 2.50],
                ['label' => 'Norris Podium', 'value' => 1.90],
            ]],
        ]);

        // Finished matches
        $this->createMatch($pl, 'Everton', 'Wolverhampton', $now->copy()->subHours(24), 'finished', false, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 2.40], ['label' => 'X', 'value' => 3.30], ['label' => '2', 'value' => 2.90]]],
        ], 2, 1);

        $this->createMatch($laliga, 'Real Betis', 'Villarreal', $now->copy()->subHours(48), 'finished', false, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 2.30], ['label' => 'X', 'value' => 3.20], ['label' => '2', 'value' => 3.10]]],
        ], 0, 0);

        // Super Lig
        $superLig = League::where('slug', 'super-lig')->first();
        $this->createMatch($superLig, 'Galatasaray', 'Fenerbahce', $now->copy()->addHours(15), 'upcoming', true, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 2.10], ['label' => 'X', 'value' => 3.40], ['label' => '2', 'value' => 3.30]]],
            ['name' => 'Over/Under 2.5', 'odds' => [['label' => 'Over 2.5', 'value' => 1.80], ['label' => 'Under 2.5', 'value' => 2.00]]],
        ]);

        // Handball
        $ehf = League::where('slug', 'ehf-champions-league')->first();
        $this->createMatch($ehf, 'THW Kiel', 'FC Barcelona', $now->copy()->addHours(36), 'upcoming', false, [
            ['name' => '1X2', 'odds' => [['label' => '1', 'value' => 3.20], ['label' => 'X', 'value' => 5.50], ['label' => '2', 'value' => 1.60]]],
        ]);

        // Horse Racing
        $ukHorse = League::where('slug', 'uk-horse-racing')->first();
        $this->createMatch($ukHorse, 'Ascot Gold Cup', 'Race 3', $now->copy()->addHours(18), 'upcoming', false, [
            ['name' => 'Winner', 'odds' => [
                ['label' => 'Stradivarius', 'value' => 3.50],
                ['label' => 'Kyprios', 'value' => 2.80],
                ['label' => 'Trawlerman', 'value' => 6.00],
                ['label' => 'Coltrane', 'value' => 10.00],
                ['label' => 'Trueshan', 'value' => 8.00],
            ]],
            ['name' => 'Each Way', 'odds' => [
                ['label' => 'Stradivarius EW', 'value' => 1.80],
                ['label' => 'Kyprios EW', 'value' => 1.55],
            ]],
        ]);
    }

    private function createMatch(
        $league,
        string $homeTeam,
        string $awayTeam,
        Carbon $startTime,
        string $status,
        bool $featured,
        array $markets,
        ?int $scoreHome = null,
        ?int $scoreAway = null,
    ): void {
        $match = GameMatch::create([
            'league_id' => $league->id,
            'home_team' => $homeTeam,
            'away_team' => $awayTeam,
            'start_time' => $startTime,
            'status' => $status,
            'score_home' => $scoreHome,
            'score_away' => $scoreAway,
            'is_featured' => $featured,
        ]);

        foreach ($markets as $marketData) {
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
    }
}
