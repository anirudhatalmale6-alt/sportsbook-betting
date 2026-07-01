<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetItem extends Model
{
    use HasFactory;

    protected $fillable = ['bet_id', 'odd_id', 'odd_value_at_placement'];

    protected function casts(): array
    {
        return [
            'odd_value_at_placement' => 'decimal:2',
        ];
    }

    public function bet()
    {
        return $this->belongsTo(Bet::class);
    }

    public function odd()
    {
        return $this->belongsTo(Odd::class);
    }
}
