<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\League;
use App\Models\Sport;
use Illuminate\Database\Seeder;

class LeagueSeeder extends Seeder
{
    public function run(): void
    {
        $football = Sport::where('slug', 'football')->first();
        $basketball = Sport::where('slug', 'basketball')->first();
        $volleyball = Sport::where('slug', 'volleyball')->first();
        $tennis = Sport::where('slug', 'tennis')->first();
        $f1 = Sport::where('slug', 'formula-1')->first();
        $combat = Sport::where('slug', 'combat-sports')->first();
        $tableTennis = Sport::where('slug', 'table-tennis')->first();
        $handball = Sport::where('slug', 'handball')->first();
        $amFootball = Sport::where('slug', 'american-football')->first();
        $baseball = Sport::where('slug', 'baseball')->first();
        $horseRacing = Sport::where('slug', 'horse-racing')->first();
        $netball = Sport::where('slug', 'netball')->first();

        $int = Country::where('code', 'INT')->first();
        $eng = Country::where('code', 'ENG')->first();
        $ger = Country::where('code', 'DEU')->first();
        $esp = Country::where('code', 'ESP')->first();
        $ita = Country::where('code', 'ITA')->first();
        $fra = Country::where('code', 'FRA')->first();
        $tur = Country::where('code', 'TUR')->first();
        $usa = Country::where('code', 'USA')->first();
        $bra = Country::where('code', 'BRA')->first();
        $aus = Country::where('code', 'AUS')->first();

        $leagues = [
            // Football
            ['name' => 'Premier League', 'slug' => 'premier-league', 'sport_id' => $football->id, 'country_id' => $eng->id, 'order' => 1],
            ['name' => 'La Liga', 'slug' => 'la-liga', 'sport_id' => $football->id, 'country_id' => $esp->id, 'order' => 2],
            ['name' => 'Bundesliga', 'slug' => 'bundesliga', 'sport_id' => $football->id, 'country_id' => $ger->id, 'order' => 3],
            ['name' => 'Serie A', 'slug' => 'serie-a', 'sport_id' => $football->id, 'country_id' => $ita->id, 'order' => 4],
            ['name' => 'Ligue 1', 'slug' => 'ligue-1', 'sport_id' => $football->id, 'country_id' => $fra->id, 'order' => 5],
            ['name' => 'Super Lig', 'slug' => 'super-lig', 'sport_id' => $football->id, 'country_id' => $tur->id, 'order' => 6],
            ['name' => 'Champions League', 'slug' => 'champions-league', 'sport_id' => $football->id, 'country_id' => $int->id, 'order' => 7],
            ['name' => 'Serie A Brazil', 'slug' => 'serie-a-brazil', 'sport_id' => $football->id, 'country_id' => $bra->id, 'order' => 8],
            ['name' => 'MLS', 'slug' => 'mls', 'sport_id' => $football->id, 'country_id' => $usa->id, 'order' => 9],
            ['name' => 'A-League', 'slug' => 'a-league', 'sport_id' => $football->id, 'country_id' => $aus->id, 'order' => 10],

            // Basketball
            ['name' => 'NBA', 'slug' => 'nba', 'sport_id' => $basketball->id, 'country_id' => $usa->id, 'order' => 1],
            ['name' => 'EuroLeague', 'slug' => 'euroleague', 'sport_id' => $basketball->id, 'country_id' => $int->id, 'order' => 2],
            ['name' => 'NBL', 'slug' => 'nbl', 'sport_id' => $basketball->id, 'country_id' => $aus->id, 'order' => 3],

            // Volleyball
            ['name' => 'CEV Champions League', 'slug' => 'cev-champions-league', 'sport_id' => $volleyball->id, 'country_id' => $int->id, 'order' => 1],
            ['name' => 'SuperLega', 'slug' => 'superlega', 'sport_id' => $volleyball->id, 'country_id' => $ita->id, 'order' => 2],

            // Tennis
            ['name' => 'ATP Tour', 'slug' => 'atp-tour', 'sport_id' => $tennis->id, 'country_id' => $int->id, 'order' => 1],
            ['name' => 'WTA Tour', 'slug' => 'wta-tour', 'sport_id' => $tennis->id, 'country_id' => $int->id, 'order' => 2],
            ['name' => 'Grand Slams', 'slug' => 'grand-slams', 'sport_id' => $tennis->id, 'country_id' => $int->id, 'order' => 3],

            // Formula 1
            ['name' => 'F1 World Championship', 'slug' => 'f1-world-championship', 'sport_id' => $f1->id, 'country_id' => $int->id, 'order' => 1],

            // Combat Sports
            ['name' => 'UFC', 'slug' => 'ufc', 'sport_id' => $combat->id, 'country_id' => $usa->id, 'order' => 1],
            ['name' => 'Boxing', 'slug' => 'boxing', 'sport_id' => $combat->id, 'country_id' => $int->id, 'order' => 2],

            // Table Tennis
            ['name' => 'WTT Champions', 'slug' => 'wtt-champions', 'sport_id' => $tableTennis->id, 'country_id' => $int->id, 'order' => 1],

            // Handball
            ['name' => 'EHF Champions League', 'slug' => 'ehf-champions-league', 'sport_id' => $handball->id, 'country_id' => $int->id, 'order' => 1],
            ['name' => 'Handball Bundesliga', 'slug' => 'handball-bundesliga', 'sport_id' => $handball->id, 'country_id' => $ger->id, 'order' => 2],

            // American Football
            ['name' => 'NFL', 'slug' => 'nfl', 'sport_id' => $amFootball->id, 'country_id' => $usa->id, 'order' => 1],

            // Baseball
            ['name' => 'MLB', 'slug' => 'mlb', 'sport_id' => $baseball->id, 'country_id' => $usa->id, 'order' => 1],

            // Horse Racing
            ['name' => 'UK Horse Racing', 'slug' => 'uk-horse-racing', 'sport_id' => $horseRacing->id, 'country_id' => $eng->id, 'order' => 1],
            ['name' => 'US Horse Racing', 'slug' => 'us-horse-racing', 'sport_id' => $horseRacing->id, 'country_id' => $usa->id, 'order' => 2],

            // Netball
            ['name' => 'Super Netball', 'slug' => 'super-netball', 'sport_id' => $netball->id, 'country_id' => $aus->id, 'order' => 1],
        ];

        foreach ($leagues as $league) {
            League::create($league);
        }
    }
}
