<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallationCompleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'commentaire' => ['nullable', 'string'],
        ];
    }
}

