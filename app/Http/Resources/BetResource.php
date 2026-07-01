<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'stake' => $this->stake,
            'potential_win' => $this->potential_win,
            'status' => $this->status,
            'items' => BetItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
        ];
    }
}
