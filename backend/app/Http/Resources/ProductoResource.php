<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    /**
     * Define la información enviada al frontend.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'precio' => (float) $this->precio,
            'stock' => (int) $this->stock,

            /*
             * Estado calculado para facilitar la visualización
             * del inventario en React.
             */
            'estado_stock' => match (true) {
                $this->stock === 0 => 'Agotado',
                $this->stock <= 5 => 'Stock bajo',
                default => 'Disponible',
            },

            'created_at' => $this->created_at
                ?->toISOString(),

            'updated_at' => $this->updated_at
                ?->toISOString(),
        ];
    }
}