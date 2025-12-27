<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EligibiliteStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resultat' => ['required', 'in:eligible,non_eligible'],
            'commentaire' => ['nullable', 'string'],
        ];
    }
}

