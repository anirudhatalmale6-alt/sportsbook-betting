<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceBetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stake' => 'required|numeric|min:0.01',
            'selections' => 'required|array|min:1',
            'selections.*.odd_id' => 'required|integer|exists:odds,id',
        ];
    }
}
