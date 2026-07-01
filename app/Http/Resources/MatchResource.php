<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'api_id' => $this->api_id,
            'league_id' => $this->league_id,
            'home_team' => $this->home_team,
            'away_team' => $this->away_team,
            'home_team_logo' => $this->home_team_logo,
            'away_team_logo' => $this->away_team_logo,
            'start_time' => $this->start_time,
            'status' => $this->status,
            'score_home' => $this->score_home,
            'score_away' => $this->score_away,
            'is_featured' => $this->is_featured,
            'league' => new LeagueResource($this->whenLoaded('league')),
            'markets' => MarketResource::collection($this->whenLoaded('markets')),
        ];
    }
}
