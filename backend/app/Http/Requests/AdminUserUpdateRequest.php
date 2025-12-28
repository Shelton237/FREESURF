<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AdminUserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id ?? null;
        return [
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email,'.$userId],
            'role' => ['required','in:backoffice,technicien'],
            'password' => ['nullable', Password::defaults()],
        ];
    }
}

