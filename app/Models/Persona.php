<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{

    // Nuestro modelo para la base de datos

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'personas';

    // 2.- Para sacar todos los usuarios q esten relacionados con personas
    // es una relacion de UNO a MUCHOS
    public function usuario()
    {
        return $this->hasMany('App\Models\User'); // se dirige hacia usuarios
    }

    // 2.- Para sacar todos los Socios q esten relacionados con personas
    // es una relacion de UNO a MUCHOS
    public function socio()
    {
        return $this->hasMany('App\Models\Socio'); // se dirige hacia socio
    }

    // 2.- Para sacar todos los empleados q esten relacionados con personas
    // es una relacion de UNO a MUCHOS
    public function empleado()
    {
        return $this->hasMany('App\Models\Empleado'); // se dirige hacia empleado
    }
}
