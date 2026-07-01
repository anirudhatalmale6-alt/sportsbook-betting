<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    use HasFactory;

    protected $fillable = ['match_id', 'name', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function match()
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function odds()
    {
        return $this->hasMany(Odd::class);
    }
}
