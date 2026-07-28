<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VentaDetalleResource extends JsonResource
{
    /**
     * Información de cada concepto incluido en el ticket.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'tipo' => $this->id_producto !== null
                ? 'Producto'
                : 'Membresía',

            'cantidad' => (int) $this->cantidad,
            'subtotal' => (float) $this->subtotal,

            'producto' => $this->whenLoaded(
                'producto',
                function () {
                    if (!$this->producto) {
                        return null;
                    }

                    return [
                        'id' => $this->producto->id,
                        'nombre' => $this->producto->nombre,
                        'precio_unitario' => (float) $this->producto->precio,
                    ];
                }
            ),

            'membresia' => $this->whenLoaded(
                'membresia',
                function () {
                    if (!$this->membresia) {
                        return null;
                    }

                    return [
                        'id' => $this->membresia->id,
                        'nombre' => $this->membresia->nombre,
                        'precio_unitario' => (float) $this->membresia->precio,
                        'duracion_dias' => (int) $this->membresia->duracion_dias,
                    ];
                }
            ),

            'created_at' => $this->created_at
                ?->toISOString(),
        ];
    }
}