<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sports',
            'icon' => 'nullable|string|max:255',
            'order' => 'sometimes|integer',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
