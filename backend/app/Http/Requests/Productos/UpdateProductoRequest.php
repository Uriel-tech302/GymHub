<?php

namespace App\Http\Requests\Productos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends FormRequest
{
    /**
     * La autorización se controla mediante middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Limpia el nombre cuando sea enviado.
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
     * Reglas para actualizar un producto.
     */
    public function rules(): array
    {
        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'min:2',
                'max:150',
                Rule::unique('productos', 'nombre')
                    ->ignore($this->route('producto')),
            ],

            'precio' => [
                'sometimes',
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
                'decimal:0,2',
            ],

            'stock' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
                'max:1000000',
            ],
        ];
    }

    /**
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.min' => 'El nombre debe contener al menos 2 caracteres.',
            'nombre.max' => 'El nombre no puede superar los 150 caracteres.',
            'nombre.unique' => 'Ya existe otro producto con este nombre.',

            'precio.required' => 'El precio del producto es obligatorio.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.min' => 'El precio debe ser mayor que cero.',
            'precio.max' => 'El precio supera el valor máximo permitido.',
            'precio.decimal' => 'El precio puede tener como máximo 2 decimales.',

            'stock.required' => 'La existencia del producto es obligatoria.',
            'stock.integer' => 'La existencia debe ser un número entero.',
            'stock.min' => 'La existencia no puede ser negativa.',
            'stock.max' => 'La existencia supera el valor máximo permitido.',
        ];
    }
}