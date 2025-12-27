<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:30'],
            'type' => ['required', 'in:domicile,entreprise'],
            'email_facturation' => ['nullable', 'email'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'partner_id' => ['nullable', 'exists:partners,id'],
            'bts_id' => ['nullable', 'exists:bts,id'],
        ];
    }
}

