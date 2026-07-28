<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * La autorización se controla mediante el middleware de administrador.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza el nombre y correo antes de validar.
     */
    protected function prepareForValidation(): void
    {
        $datos = [];

        if ($this->has('name') && is_string($this->name)) {
            $datos['name'] = trim($this->name);
        }

        if ($this->has('email') && is_string($this->email)) {
            $datos['email'] = strtolower(trim($this->email));
        }

        if (!empty($datos)) {
            $this->merge($datos);
        }
    }

    /**
     * Reglas para actualizar un usuario.
     *
     * La contraseña es opcional. Si no se envía,
     * se conserva la contraseña actual.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'email' => [
                'sometimes',
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')
                    ->ignore($this->route('user')),
            ],

            'password' => [
                'sometimes',
                'nullable',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],

            'role' => [
                'sometimes',
                'required',
                Rule::in([
                    'Administrador',
                    'Empleado',
                    'Cliente',
                ]),
            ],

            'fecha_vencimiento' => [
                'sometimes',
                'nullable',
                'date',
            ],
        ];
    }

    /**
     * Mensajes personalizados.
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
            'email.unique' => 'Este correo electrónico ya pertenece a otro usuario.',

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
