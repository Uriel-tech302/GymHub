<?php

namespace App\Http\Requests\Ventas;

use Illuminate\Foundation\Http\FormRequest;

class IndexVentaRequest extends FormRequest
{
    /**
     * El acceso se controla mediante los middleware
     * auth:sanctum y role configurados en las rutas.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Valida los filtros del historial de ventas.
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

            'id_cliente' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'id_empleado' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'fecha_desde' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'fecha_hasta' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:fecha_desde',
            ],

            'total_min' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'total_max' => [
                'nullable',
                'numeric',
                'min:0',
                'gte:total_min',
            ],
        ];
    }

    /**
     * Mensajes de validación en español.
     */
    public function messages(): array
    {
        return [
            'search.string' => 'La búsqueda debe ser un texto válido.',
            'search.max' => 'La búsqueda no puede superar los 150 caracteres.',

            'per_page.integer' => 'La cantidad por página debe ser un número entero.',
            'per_page.min' => 'Debe solicitar al menos un registro por página.',
            'per_page.max' => 'No se permiten más de 50 registros por página.',

            'id_cliente.integer' => 'El identificador del cliente no es válido.',
            'id_cliente.exists' => 'El cliente seleccionado no existe.',

            'id_empleado.integer' => 'El identificador del empleado no es válido.',
            'id_empleado.exists' => 'El empleado seleccionado no existe.',

            'fecha_desde.date_format' => 'La fecha inicial debe tener el formato AAAA-MM-DD.',
            'fecha_hasta.date_format' => 'La fecha final debe tener el formato AAAA-MM-DD.',
            'fecha_hasta.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha inicial.',

            'total_min.numeric' => 'El total mínimo debe ser numérico.',
            'total_min.min' => 'El total mínimo no puede ser negativo.',

            'total_max.numeric' => 'El total máximo debe ser numérico.',
            'total_max.min' => 'El total máximo no puede ser negativo.',
            'total_max.gte' => 'El total máximo debe ser igual o mayor al total mínimo.',
        ];
    }
}