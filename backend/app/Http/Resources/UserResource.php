<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Define los datos que puede recibir el frontend.
     *
     * Nunca enviamos la contraseña ni el token de recuerdo.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'role' => $this->role,
            'foto_perfil' => $this->foto_perfil,

            'fecha_vencimiento' => $this->fecha_vencimiento
                ?->format('Y-m-d'),

            'created_at' => $this->created_at
                ?->toISOString(),

            'updated_at' => $this->updated_at
                ?->toISOString(),
        ];
    }
}