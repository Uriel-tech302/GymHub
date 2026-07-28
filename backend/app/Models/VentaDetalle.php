<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaDetalle extends Model
{
    use HasFactory;

    /**
     * Campos permitidos para asignación masiva.
     */
    protected $fillable = [
        'id_venta',
        'id_producto',
        'id_membresia',
        'cantidad',
        'subtotal',
    ];

    /**
     * Conversiones automáticas.
     */
    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Venta a la que pertenece el detalle.
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(
            Venta::class,
            'id_venta'
        );
    }

    /**
     * Producto vendido.
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(
            Producto::class,
            'id_producto'
        );
    }

    /**
     * Membresía vendida.
     */
    public function membresia(): BelongsTo
    {
        return $this->belongsTo(
            Membresia::class,
            'id_membresia'
        );
    }
}