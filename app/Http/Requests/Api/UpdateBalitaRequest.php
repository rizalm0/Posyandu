<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBalitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['sometimes', 'required', 'string', 'max:120'],
            'date_of_birth' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'gender' => ['sometimes', 'required', Rule::in(['L', 'P'])],
            'parent_name' => ['sometimes', 'required', 'string', 'max:120'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:25'],
            'address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'registered_at' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
