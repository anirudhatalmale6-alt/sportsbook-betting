<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOddsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'markets' => 'required|array|min:1',
            'markets.*.name' => 'required|string|max:255',
            'markets.*.odds' => 'required|array|min:1',
            'markets.*.odds.*.label' => 'required|string|max:255',
            'markets.*.odds.*.value' => 'required|numeric|min:1.01',
        ];
    }
}
