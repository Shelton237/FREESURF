<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BtsStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ville' => ['required', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'composants' => ['nullable', 'array'],
        ];
    }
}

