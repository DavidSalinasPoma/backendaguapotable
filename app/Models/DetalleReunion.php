<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleReunion extends Model
{
    use HasFactory;

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'reunion_detalles';

    // relacion de muchos a uno inversa(muchos a uno)
    public function socio()
    {
        return $this->belongsTo('App\Models\Socio', 'socio_id'); // Recibe a Socio
    }
    // relacion de muchos a uno inversa(muchos a uno)
    public function reunion()
    {
        return $this->belongsTo('App\Models\Reunion', 'reunion_id'); // Recibe a Reunion
    }
}
