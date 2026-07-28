<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * La autorización principal se realiza mediante el middleware
     * de administrador configurado en las rutas.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Limpia algunos datos antes de aplicar las validaciones.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->name)
                ? trim($this->name)
                : $this->name,

            'email' => is_string($this->email)
                ? strtolower(trim($this->email))
                : $this->email,
        ]);
    }

    /**
     * Reglas para crear un usuario desde el panel administrativo.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:150',
                'unique:users,email',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[0-9]{10,15}$/',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],

            'role' => [
                'required',
                Rule::in([
                    'Administrador',
                    'Empleado',
                    'Cliente',
                ]),
            ],

            'fecha_vencimiento' => [
                'nullable',
                'date',
            ],
        ];
    }

    /**
     * Mensajes personalizados para las respuestas JSON.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre completo es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.min' => 'El nombre debe contener al menos 2 caracteres.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'email.max' => 'El correo no puede superar los 150 caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado.',

            'telefono.max' => 'El teléfono no puede superar los 20 caracteres.',
            'telefono.regex' => 'El teléfono debe contener entre 10 y 15 dígitos y puede comenzar con el símbolo +.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.mixed' => 'La contraseña debe incluir mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe incluir al menos un número.',
            'password.symbols' => 'La contraseña debe incluir al menos un carácter especial.',

            'role.required' => 'El rol del usuario es obligatorio.',
            'role.in' => 'El rol seleccionado no es válido.',

            'fecha_vencimiento.date' => 'La fecha de vencimiento no es válida.',
        ];
    }
}
