<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'facturas';

    // relacion de muchos a uno inversa(muchos a uno)
    public function consumo()
    {
        return $this->belongsTo('App\Models\Consumo', 'consumo_id'); // Recibe a Consumo
    }
}
