<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'language' => 'sometimes|string|max:5',
            'odds_format' => 'sometimes|in:decimal,fractional',
        ];
    }
}
