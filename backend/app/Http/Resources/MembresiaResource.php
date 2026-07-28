<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembresiaResource extends JsonResource
{
    /**
     * Información que recibirá el frontend.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'precio' => (float) $this->precio,
            'duracion_dias' => (int) $this->duracion_dias,

            'duracion_texto' => $this->duracion_dias === 1
                ? '1 día'
                : "{$this->duracion_dias} días",

            'created_at' => $this->created_at
                ?->toISOString(),

            'updated_at' => $this->updated_at
                ?->toISOString(),
        ];
    }
}