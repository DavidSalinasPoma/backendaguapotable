<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lista extends Model
{
    use HasFactory;
    // Nuestro modelo para la base de datos

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'listas';

    // relacion de muchos a uno inversa(muchos a uno)
    public function apertura()
    {
        return $this->belongsTo('App\Models\Apertura', 'apertura_id'); // Recibe a Apertura
    }
    // relacion de muchos a uno inversa(muchos a uno)
    public function socio()
    {
        return $this->belongsTo('App\Models\Socio', 'socio_id'); // Recibe a Socio
    }
}
