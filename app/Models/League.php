<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'api_key', 'sport_id', 'country_id', 'is_active', 'order'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function matches()
    {
        return $this->hasMany(GameMatch::class, 'league_id');
    }
}
