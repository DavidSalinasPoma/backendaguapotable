<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barrio extends Model
{
    use HasFactory;

    // Nuestro modelo para la base de datos

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'barrios';

    // 2.- Para sacar todos los usuarios q esten relacionados con socios
    // es una relacion de UNO a MUCHOS
    public function socio()
    {
        return $this->hasMany('App\Models\Socio'); // se dirige hacia socios
    }
}
