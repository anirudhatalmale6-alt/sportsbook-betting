<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BetItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bet_id' => $this->bet_id,
            'odd_id' => $this->odd_id,
            'odd_value_at_placement' => $this->odd_value_at_placement,
            'odd' => new OddResource($this->whenLoaded('odd')),
        ];
    }
}
