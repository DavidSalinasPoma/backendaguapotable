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

    // relacion de muchos a uno inversa(muchos a uno)
    public function barrio()
    {
        return $this->belongsTo('App\Models\Barrio', 'barrio_id'); // Recibe a Barrio
    }

    // Para sacar todos los consumos q esten relacionados con socios
    // es una relacion de UNO a MUCHOS
    public function consumo()
    {
        return $this->hasMany('App\Models\Consumo'); // se dirige hacia Consumos
    }

    // Se dirige hacia lista
    public function lista()
    {
        return $this->hasMany('App\Models\Lista'); // se dirige hacia Lista
    }

    // Se dirige hacia detalle reunion
    public function detallereunion()
    {
        return $this->hasMany('App\Models\DetalleReunion'); // se dirige hacia detalle reunion
    }
}
