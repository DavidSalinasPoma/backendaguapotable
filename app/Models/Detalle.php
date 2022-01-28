<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle extends Model
{
    use HasFactory;

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'detalles';


    // relacion de muchos a uno inversa(muchos a uno)
    public function producto()
    {
        return $this->belongsTo('App\Models\Producto', 'producto_id'); // Recibe a Apertura
    }
}
