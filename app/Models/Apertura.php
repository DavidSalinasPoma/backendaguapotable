<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apertura extends Model
{
    use HasFactory;

    // Nuestro modelo para la base de datos

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'aperturas';

    // 2.- Para sacar todos los consumos q esten relacionados con socios
    // es una relacion de UNO a MUCHOS
    public function consumo()
    {
        return $this->hasMany('App\Models\Consumo'); // se dirige hacia Consumos
    }

    public function lista()
    {
        return $this->hasMany('App\Models\Lista'); // se dirige hacia Lista
    }
}
