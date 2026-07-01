<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        $sports = [
            ['name' => 'Football', 'slug' => 'football', 'icon' => 'football', 'order' => 1],
            ['name' => 'Basketball', 'slug' => 'basketball', 'icon' => 'basketball', 'order' => 2],
            ['name' => 'Volleyball', 'slug' => 'volleyball', 'icon' => 'volleyball', 'order' => 3],
            ['name' => 'Tennis', 'slug' => 'tennis', 'icon' => 'tennis', 'order' => 4],
            ['name' => 'Formula 1', 'slug' => 'formula-1', 'icon' => 'racing', 'order' => 5],
            ['name' => 'Combat Sports', 'slug' => 'combat-sports', 'icon' => 'boxing', 'order' => 6],
            ['name' => 'Table Tennis', 'slug' => 'table-tennis', 'icon' => 'table-tennis', 'order' => 7],
            ['name' => 'Netball', 'slug' => 'netball', 'icon' => 'netball', 'order' => 8],
            ['name' => 'Handball', 'slug' => 'handball', 'icon' => 'handball', 'order' => 9],
            ['name' => 'American Football', 'slug' => 'american-football', 'icon' => 'american-football', 'order' => 10],
            ['name' => 'Baseball', 'slug' => 'baseball', 'icon' => 'baseball', 'order' => 11],
            ['name' => 'Horse Racing', 'slug' => 'horse-racing', 'icon' => 'horse-racing', 'order' => 12],
        ];

        foreach ($sports as $sport) {
            Sport::create($sport);
        }
    }
}
