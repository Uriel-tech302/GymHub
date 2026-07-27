<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    protected $fillable = ['id_venta', 'id_producto', 'id_membresia', 'cantidad', 'subtotal'];
}
