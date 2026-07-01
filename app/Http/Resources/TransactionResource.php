<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'amount' => $this->amount,
            'balance_after' => $this->balance_after,
            'description' => $this->description,
            'admin_id' => $this->admin_id,
            'created_at' => $this->created_at,
        ];
    }
}
