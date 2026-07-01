<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeagueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sport_id' => $this->sport_id,
            'country_id' => $this->country_id,
            'is_active' => $this->is_active,
            'order' => $this->order,
            'sport' => new SportResource($this->whenLoaded('sport')),
            'country' => new CountryResource($this->whenLoaded('country')),
            'matches_count' => $this->whenCounted('matches'),
        ];
    }
}
