<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'balance' => $this->balance,
            'role' => $this->role,
            'status' => $this->status,
            'language' => $this->language,
            'odds_format' => $this->odds_format,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
        ];
    }
}
