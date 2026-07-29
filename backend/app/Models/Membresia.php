<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membresia extends Model
{
    use HasFactory;

    /**
     * Campos permitidos para asignación masiva.
     */
    protected $fillable = [
        'nombre',
        'precio',
        'duracion_dias',
    ];

    /**
     * Convierte los valores al tipo correcto.
     */
    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'duracion_dias' => 'integer',
        ];
    }

    /**
     * Detalles de ventas donde se incluyó esta membresía.
     */
    public function detallesVenta(): HasMany
    {
        return $this->hasMany(
            VentaDetalle::class,
            'id_membresia'
        );
    }
}