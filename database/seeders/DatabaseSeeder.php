<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SportSeeder::class,
            CountrySeeder::class,
            LeagueSeeder::class,
            MatchSeeder::class,
        ]);
    }
}
