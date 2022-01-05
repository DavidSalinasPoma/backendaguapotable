<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Socio extends Model
{
    use HasFactory;

    // Nuestro modelo para la base de datos

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'socios';

    // relacion de muchos a uno inversa(muchos a uno)
    public function persona()
    {
        return $this->belongsTo('App\Models\Persona', 'persona_id'); // Recibe a Persona
    }
}
