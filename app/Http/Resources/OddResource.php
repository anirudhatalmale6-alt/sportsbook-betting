<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OddResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'market_id' => $this->market_id,
            'label' => $this->label,
            'value' => $this->value,
            'is_active' => $this->is_active,
        ];
    }
}
