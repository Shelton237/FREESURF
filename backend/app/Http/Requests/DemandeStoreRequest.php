<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DemandeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:abonnement,reabonnement'],
            'nom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:30'],
            'email_facturation' => ['nullable', 'email'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'commentaire' => ['nullable', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'client_code' => [
                Rule::requiredIf(fn () => $this->input('type') === 'reabonnement'),
                'string',
                'exists:clients,code',
            ],
        ];
    }
}
