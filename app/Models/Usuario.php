<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    //Modelo para la base de datos

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    // protected $table = 'usuarios';

    // relacion de uno a muchos inversa(muchos a uno)
    public function personas()
    {
        return $this->belongsTo('App\Persona', 'persona_id'); // Recibe a Persona
    }
}
