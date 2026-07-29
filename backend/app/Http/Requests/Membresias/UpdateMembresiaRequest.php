<?php

namespace App\Http\Requests\Membresias;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMembresiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Limpia el nombre únicamente cuando fue enviado.
     */
    protected function prepareForValidation(): void
    {
        if (
            $this->has('nombre') &&
            is_string($this->nombre)
        ) {
            $this->merge([
                'nombre' => trim($this->nombre),
            ]);
        }
    }

    /**
     * Todos los campos son opcionales en una actualización parcial.
     */
    public function rules(): array
    {
        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'min:2',
                'max:100',
                Rule::unique('membresias', 'nombre')
                    ->ignore($this->route('membresia')),
            ],

            'precio' => [
                'sometimes',
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
                'decimal:0,2',
            ],

            'duracion_dias' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                'max:3650',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la membresía es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.min' => 'El nombre debe contener al menos 2 caracteres.',
            'nombre.max' => 'El nombre no puede superar los 100 caracteres.',
            'nombre.unique' => 'Ya existe otra membresía con este nombre.',

            'precio.required' => 'El precio de la membresía es obligatorio.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.min' => 'El precio debe ser mayor que cero.',
            'precio.max' => 'El precio supera el valor máximo permitido.',
            'precio.decimal' => 'El precio puede tener como máximo 2 decimales.',

            'duracion_dias.required' => 'La duración de la membresía es obligatoria.',
            'duracion_dias.integer' => 'La duración debe indicarse en días enteros.',
            'duracion_dias.min' => 'La membresía debe durar al menos un día.',
            'duracion_dias.max' => 'La duración no puede superar los 3650 días.',
        ];
    }
}
