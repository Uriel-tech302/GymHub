<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VentaResource extends JsonResource
{
    /**
     * Información completa de la venta.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'fecha' => $this->fecha
                ?->toISOString(),

            'total' => (float) $this->total,

            'empleado' => $this->whenLoaded(
                'empleado',
                function () {
                    if (!$this->empleado) {
                        return null;
                    }

                    return [
                        'id' => $this->empleado->id,
                        'name' => $this->empleado->name,
                        'email' => $this->empleado->email,
                        'role' => $this->empleado->role,
                    ];
                }
            ),

            'cliente' => $this->whenLoaded(
                'cliente',
                function () {
                    if (!$this->cliente) {
                        return null;
                    }

                    return [
                        'id' => $this->cliente->id,
                        'name' => $this->cliente->name,
                        'email' => $this->cliente->email,
                        'telefono' => $this->cliente->telefono,

                        'fecha_vencimiento' =>
                            $this->cliente->fecha_vencimiento
                                ?->format('Y-m-d'),
                    ];
                }
            ),

            'detalles' => VentaDetalleResource::collection(
                $this->whenLoaded('detalles')
            ),

            'created_at' => $this->created_at
                ?->toISOString(),

            'updated_at' => $this->updated_at
                ?->toISOString(),
        ];
    }
}