<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Permite que cualquier visitante intente iniciar sesión.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para el inicio de sesión.
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * Mensajes personalizados en español.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ];
    }
}