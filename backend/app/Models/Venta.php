<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    use HasFactory;

    /**
     * Campos permitidos para asignación masiva.
     */
    protected $fillable = [
        'fecha',
        'total',
        'id_empleado',
        'id_cliente',
    ];

    /**
     * Conversiones automáticas.
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Empleado o administrador responsable de la venta.
     */
    public function empleado(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'id_empleado'
        );
    }

    /**
     * Cliente asociado a la venta.
     *
     * Puede ser nulo cuando solamente se venden productos
     * y no se desea registrar un comprador.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'id_cliente'
        );
    }

    /**
     * Conceptos incluidos en el ticket.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(
            VentaDetalle::class,
            'id_venta'
        );
    }
}