<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = ['fecha', 'total', 'id_empleado', 'id_cliente'];

    // Relación: Una venta tiene muchos detalles
    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'id_venta');
    }
}