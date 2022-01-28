<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    use HasFactory;
    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'productos';

    // Para sacar todos los consumos q esten relacionados con socios
    // es una relacion de UNO a MUCHOS
    public function detalle()
    {
        return $this->hasMany('App\Models\Detalle'); // se dirige hacia Detalle
    }
}
