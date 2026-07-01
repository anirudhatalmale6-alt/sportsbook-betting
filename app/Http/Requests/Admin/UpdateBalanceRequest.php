<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBalanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric',
            'type' => 'required|in:deposit,withdrawal',
            'description' => 'nullable|string|max:500',
        ];
    }
}
