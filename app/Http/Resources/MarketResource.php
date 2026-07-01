<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'match_id' => $this->match_id,
            'name' => $this->name,
            'is_active' => $this->is_active,
            'odds' => OddResource::collection($this->whenLoaded('odds')),
        ];
    }
}
