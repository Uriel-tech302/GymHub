<?php

namespace App\Http\Requests\Ventas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreVentaRequest extends FormRequest
{
    /**
     * Los permisos se controlan con los middleware
     * Administrador y Empleado.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas principales para registrar una venta.
     *
     * El frontend NO envía:
     * - id_empleado
     * - precio
     * - subtotal
     * - total
     *
     * Laravel calculará esos datos usando MySQL.
     */
    public function rules(): array
    {
        return [
            'id_cliente' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'detalles' => [
                'required',
                'array',
                'min:1',
                'max:50',
            ],

            'detalles.*.id_producto' => [
                'nullable',
                'integer',
                'exists:productos,id',
            ],

            'detalles.*.id_membresia' => [
                'nullable',
                'integer',
                'exists:membresias,id',
            ],

            'detalles.*.cantidad' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],
        ];
    }

    /**
     * Validaciones adicionales.
     *
     * Cada detalle debe contener exactamente:
     * - un producto, o
     * - una membresía.
     *
     * Nunca ambos y nunca ninguno.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $detalles = $this->input('detalles', []);

                foreach ($detalles as $indice => $detalle) {
                    $tieneProducto =
                        isset($detalle['id_producto']) &&
                        $detalle['id_producto'] !== null &&
                        $detalle['id_producto'] !== '';

                    $tieneMembresia =
                        isset($detalle['id_membresia']) &&
                        $detalle['id_membresia'] !== null &&
                        $detalle['id_membresia'] !== '';

                    /*
                     * Si ambos son verdaderos o ambos son falsos,
                     * el detalle es inválido.
                     */
                    if ($tieneProducto === $tieneMembresia) {
                        $validator->errors()->add(
                            "detalles.{$indice}.tipo",
                            'Cada detalle debe incluir solamente un producto o una membresía.'
                        );
                    }

                    /*
                     * Para vender una membresía es obligatorio
                     * seleccionar al cliente que la recibirá.
                     */
                    if (
                        $tieneMembresia &&
                        !$this->filled('id_cliente')
                    ) {
                        $validator->errors()->add(
                            'id_cliente',
                            'Debes seleccionar un cliente para vender una membresía.'
                        );
                    }
                }
            },
        ];
    }

    /**
     * Mensajes de validación.
     */
    public function messages(): array
    {
        return [
            'id_cliente.integer' => 'El identificador del cliente no es válido.',
            'id_cliente.exists' => 'El cliente seleccionado no existe.',

            'detalles.required' => 'Debes agregar al menos un artículo a la venta.',
            'detalles.array' => 'Los detalles de la venta deben enviarse como una lista.',
            'detalles.min' => 'Debes agregar al menos un artículo a la venta.',
            'detalles.max' => 'No puedes agregar más de 50 conceptos en una venta.',

            'detalles.*.id_producto.integer' => 'El identificador del producto no es válido.',
            'detalles.*.id_producto.exists' => 'Uno de los productos seleccionados no existe.',

            'detalles.*.id_membresia.integer' => 'El identificador de la membresía no es válido.',
            'detalles.*.id_membresia.exists' => 'Una de las membresías seleccionadas no existe.',

            'detalles.*.cantidad.required' => 'La cantidad es obligatoria.',
            'detalles.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'detalles.*.cantidad.min' => 'La cantidad debe ser de al menos una unidad.',
            'detalles.*.cantidad.max' => 'La cantidad no puede superar las 100 unidades.',
        ];
    }
}