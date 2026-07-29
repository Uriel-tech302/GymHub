<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Limpia los datos antes de validarlos.
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

        if ($this->has('telefono') && is_string($this->telefono)) {
            $datos['telefono'] = trim($this->telefono);
        }

        if (!empty($datos)) {
            $this->merge($datos);
        }
    }

    /**
     * El usuario únicamente puede actualizar sus datos personales.
     *
     * No puede modificar su rol ni su fecha de vencimiento.
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
                    ->ignore($this->user()->id),
            ],

            'telefono' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[0-9]{10,15}$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre completo es obligatorio.',
            'name.min' => 'El nombre debe contener al menos 2 caracteres.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'email.unique' => 'Este correo electrónico ya pertenece a otro usuario.',

            'telefono.max' => 'El teléfono no puede superar los 20 caracteres.',
            'telefono.regex' => 'El teléfono debe contener entre 10 y 15 dígitos y puede comenzar con +.',
        ];
    }
}