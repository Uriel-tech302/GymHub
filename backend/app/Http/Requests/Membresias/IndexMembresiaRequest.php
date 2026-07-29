<?php

namespace App\Http\Requests\Membresias;

use Illuminate\Foundation\Http\FormRequest;

class IndexMembresiaRequest extends FormRequest
{
    /**
     * Los permisos de consulta se controlan mediante
     * los middleware definidos en routes/api.php.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Valida los filtros enviados mediante parámetros de la URL.
     */
    public function rules(): array
    {
        return [
            'search' => [
                'nullable',
                'string',
                'max:150',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],

            'precio_min' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],

            'precio_max' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],

            'duracion_min' => [
                'nullable',
                'integer',
                'min:1',
                'max:3650',
            ],

            'duracion_max' => [
                'nullable',
                'integer',
                'min:1',
                'max:3650',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'search.string' => 'La búsqueda debe ser un texto válido.',
            'search.max' => 'La búsqueda no puede superar los 150 caracteres.',

            'per_page.integer' => 'La cantidad por página debe ser un número entero.',
            'per_page.min' => 'Debe solicitar al menos un registro por página.',
            'per_page.max' => 'No se permiten más de 50 registros por página.',

            'precio_min.numeric' => 'El precio mínimo debe ser numérico.',
            'precio_min.min' => 'El precio mínimo no puede ser negativo.',
            'precio_max.numeric' => 'El precio máximo debe ser numérico.',
            'precio_max.min' => 'El precio máximo no puede ser negativo.',

            'duracion_min.integer' => 'La duración mínima debe ser un número entero.',
            'duracion_min.min' => 'La duración mínima debe ser de al menos un día.',
            'duracion_max.integer' => 'La duración máxima debe ser un número entero.',
            'duracion_max.min' => 'La duración máxima debe ser de al menos un día.',
        ];
    }
}