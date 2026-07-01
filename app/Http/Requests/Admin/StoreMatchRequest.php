<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'league_id' => 'required|exists:leagues,id',
            'home_team' => 'required|string|max:255',
            'away_team' => 'required|string|max:255',
            'home_team_logo' => 'nullable|string|max:255',
            'away_team_logo' => 'nullable|string|max:255',
            'start_time' => 'required|date',
            'status' => 'sometimes|in:upcoming,live,finished,cancelled',
            'is_featured' => 'sometimes|boolean',
        ];
    }
}
