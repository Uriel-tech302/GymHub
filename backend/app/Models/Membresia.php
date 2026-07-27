<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membresia extends Model
{
    protected $fillable = ['nombre', 'precio', 'duracion_dias'];
}
