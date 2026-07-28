<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    /**
     * Campos permitidos para asignación masiva.
     */
    protected $fillable = [
        'nombre',
        'precio',
        'stock',
    ];

    /**
     * Conversiones automáticas.
     */
    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    /**
     * Detalles de venta en los que aparece el producto.
     */
    public function detallesVenta(): HasMany
    {
        return $this->hasMany(
            VentaDetalle::class,
            'id_producto'
        );
    }
}