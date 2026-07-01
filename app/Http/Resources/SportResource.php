<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'leagues_count' => $this->whenCounted('leagues'),
            'matches_count' => $this->whenCounted('matches'),
            'leagues' => LeagueResource::collection($this->whenLoaded('leagues')),
        ];
    }
}
