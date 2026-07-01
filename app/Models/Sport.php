<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'order', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function leagues()
    {
        return $this->hasMany(League::class);
    }

    public function matches()
    {
        return $this->hasManyThrough(GameMatch::class, League::class, 'sport_id', 'league_id');
    }
}
