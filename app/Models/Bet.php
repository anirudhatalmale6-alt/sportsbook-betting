<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'stake', 'potential_win', 'status'];

    protected function casts(): array
    {
        return [
            'stake' => 'decimal:2',
            'potential_win' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(BetItem::class);
    }
}
