<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeagueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:leagues',
            'sport_id' => 'required|exists:sports,id',
            'country_id' => 'required|exists:countries,id',
            'is_active' => 'sometimes|boolean',
            'order' => 'sometimes|integer',
        ];
    }
}
