<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'International', 'code' => 'INT', 'flag_emoji' => null],
            ['name' => 'England', 'code' => 'ENG', 'flag_emoji' => null],
            ['name' => 'Germany', 'code' => 'DEU', 'flag_emoji' => null],
            ['name' => 'Spain', 'code' => 'ESP', 'flag_emoji' => null],
            ['name' => 'Italy', 'code' => 'ITA', 'flag_emoji' => null],
            ['name' => 'France', 'code' => 'FRA', 'flag_emoji' => null],
            ['name' => 'Turkey', 'code' => 'TUR', 'flag_emoji' => null],
            ['name' => 'USA', 'code' => 'USA', 'flag_emoji' => null],
            ['name' => 'Brazil', 'code' => 'BRA', 'flag_emoji' => null],
            ['name' => 'Australia', 'code' => 'AUS', 'flag_emoji' => null],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
