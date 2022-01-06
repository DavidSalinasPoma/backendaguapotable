<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;
    // Nuestro modelo para la base de datos

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'eventos';
}
