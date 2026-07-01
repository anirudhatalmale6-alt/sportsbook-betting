<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'league_id', 'home_team', 'away_team', 'home_team_logo', 'away_team_logo',
        'start_time', 'status', 'score_home', 'score_away', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'is_featured' => 'boolean',
        ];
    }

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function markets()
    {
        return $this->hasMany(Market::class, 'match_id');
    }
}
