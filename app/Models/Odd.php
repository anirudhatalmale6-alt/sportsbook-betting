<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Odd extends Model
{
    use HasFactory;

    protected $fillable = ['market_id', 'label', 'value', 'is_active'];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function market()
    {
        return $this->belongsTo(Market::class);
    }
}
