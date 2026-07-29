<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    /**
     * Esta petición es pública porque el usuario
     * todavía no puede iniciar sesión.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza el correo antes de validarlo.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->email)) {
            $this->merge([
                'email' => strtolower(trim($this->email)),
            ]);
        }
    }

    /**
     * Validaciones para solicitar la recuperación.
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:150',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'email.max' => 'El correo no puede superar los 150 caracteres.',
        ];
    }
}