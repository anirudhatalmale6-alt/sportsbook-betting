<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'league_id' => 'sometimes|exists:leagues,id',
            'home_team' => 'sometimes|string|max:255',
            'away_team' => 'sometimes|string|max:255',
            'home_team_logo' => 'nullable|string|max:255',
            'away_team_logo' => 'nullable|string|max:255',
            'start_time' => 'sometimes|date',
            'status' => 'sometimes|in:upcoming,live,finished,cancelled',
            'score_home' => 'nullable|integer|min:0',
            'score_away' => 'nullable|integer|min:0',
            'is_featured' => 'sometimes|boolean',
        ];
    }
}
